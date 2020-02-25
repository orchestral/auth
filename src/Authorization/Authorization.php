<?php

namespace Orchestra\Authorization;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Orchestra\Contracts\Authorization\Authorizable;
use Orchestra\Contracts\Authorization\Authorization as AuthorizationContract;
use Orchestra\Contracts\Memory\Provider;
use Orchestra\Memory\Memorizable;
use Orchestra\Support\Keyword;
use RuntimeException;

class Authorization implements AuthorizationContract
{
    use Permission, Memorizable;

    /**
     * ACL instance name.
     *
     * @var string
     */
    protected $name;

    /**
     * Construct a new object.
     */
    public function __construct(string $name, Provider $memory = null)
    {
        $this->name = $name;

        $this->roles = new Role();
        $this->actions = new Action();

        $this->roles->add('guest');
        $this->attach($memory);
    }

    /**
     * Set authenticator.
     *
     * @return $this
     */
    public function setAuthenticator(Guard $auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Bind current ACL instance with a Memory instance.
     *
     * @throws \RuntimeException if $memory has been attached
     *
     * @return $this
     */
    public function attach(Provider $memory = null)
    {
        if ($this->attached() && $memory !== $this->memory) {
            throw new RuntimeException("Unable to assign multiple Orchestra\Memory instance.");
        }

        // since we already check instanceof Orchestra\Memory\Provider,
        // It safe to just check for not NULL.
        if (! \is_null($memory)) {
            $this->setMemoryProvider($memory);
            $this->initiate();
        }

        return $this;
    }

    /**
     * Initiate ACL data from memory.
     *
     * @return $this
     */
    protected function initiate()
    {
        $name = $this->name;
        $data = ['acl' => [], 'actions' => [], 'roles' => []];
        $data = \array_merge($data, $this->memory->get("acl_{$name}", []));

        // Loop through all the roles and actions in memory and add it to
        // this ACL instance.
        $this->roles->attach($data['roles']);
        $this->actions->attach($data['actions']);

        // Loop through all the ACL in memory and add it to this ACL
        // instance.
        foreach ($data['acl'] as $id => $allow) {
            list($role, $action) = \explode(':', $id);
            $this->assign($role, $action, $allow);
        }

        return $this->sync();
    }

    /**
     * Assign single or multiple $roles + $actions to have access.
     *
     * @param  string|array  $roles
     * @param  string|array  $actions
     *
     * @return $this
     */
    public function allow($roles, $actions, bool $allow = true)
    {
        $this->setAuthorization($roles, $actions, $allow);

        return $this->sync();
    }

    /**
     * Verify whether current user has sufficient roles to access the
     * actions based on available type of access.
     *
     * @throws \InvalidArgumentException
     */
    public function can(string $action): bool
    {
        return $this->checkAuthorization($this->getUserRoles(), $action);
    }

    /**
     * Verify whether current user has sufficient roles to access the
     * actions based on available type of access if the action exist.
     */
    public function canIf(string $action): bool
    {
        $roles = $this->getUserRoles();
        $action = Keyword::make($action);

        if (\is_null($this->actions->search($action))) {
            return false;
        }

        return $this->checkAuthorization($roles, $action);
    }

    /**
     * Verify whether current user has sufficient roles to access the
     * actions based on available type of access.
     *
     * @throws \InvalidArgumentException
     */
    public function canAs(Authorizable $user, string $action): bool
    {
        $this->setUser($user);

        $permission = $this->can($action);

        $this->revokeUser();

        return $permission;
    }

    /**
     * Verify whether current user has sufficient roles to access the
     * actions based on available type of access if the action exist.
     */
    public function canIfAs(Authorizable $user, string $action): bool
    {
        $this->setUser($user);

        $permission = $this->canIf($action);

        $this->revokeUser();

        return $permission;
    }

    /**
     * Verify whether given roles has sufficient roles to access the
     * actions based on available type of access.
     *
     * @param  string|array  $roles
     *
     * @throws \InvalidArgumentException
     */
    public function check($roles, string $action): bool
    {
        return $this->checkAuthorization($roles, $action);
    }

    /**
     * Shorthand function to deny access for single or multiple
     * $roles and $actions.
     *
     * @param  string|array  $roles
     * @param  string|array  $actions
     *
     * @return $this
     */
    public function deny($roles, $actions)
    {
        return $this->allow($roles, $actions, false);
    }

    /**
     * Sync memory with ACL instance, make sure anything that added before
     * ->with($memory) got called is appended to memory as well.
     *
     * @return $this
     */
    public function sync()
    {
        if ($this->attached()) {
            $name = $this->name;

            $this->memory->put("acl_{$name}", [
                'acl' => $this->acl,
                'actions' => $this->actions->get(),
                'roles' => $this->roles->get(),
            ]);
        }

        return $this;
    }

    /**
     * Forward call to roles or actions.
     *
     * @return mixed
     */
    public function execute(string $type, string $operation, array $parameters = [])
    {
        return $this->{$type}->{$operation}(...$parameters);
    }

    /**
     * Magic method to mimic roles and actions manipulation.
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        [$type, $operation] = $this->resolveDynamicExecution($method);

        $response = $this->execute($type, $operation, $parameters);

        if ($operation === 'has') {
            return $response;
        }

        return $this->sync();
    }

    /**
     * Dynamically resolve operation name especially to resolve attach and
     * detach multiple actions or roles.
     *
     * @throws \InvalidArgumentException
     */
    protected function resolveDynamicExecution(string $method): array
    {
        // Preserve legacy CRUD structure for actions and roles.
        $method = Str::snake($method, '_');
        $matcher = '/^(add|rename|has|get|remove|fill|attach|detach)_(role|action)(s?)$/';

        if (! \preg_match($matcher, $method, $matches)) {
            throw new InvalidArgumentException("Invalid keyword [$method]");
        }

        $type = $matches[2].'s';
        $multiple = (isset($matches[3]) && $matches[3] === 's');
        $operation = $this->resolveOperationName($matches[1], $multiple);

        return [$type, $operation];
    }

    /**
     * Dynamically resolve operation name especially when multiple
     * operation was used.
     */
    protected function resolveOperationName(string $operation, bool $multiple = true): string
    {
        if (! $multiple) {
            return $operation;
        } elseif (\in_array($operation, ['fill', 'add'])) {
            return 'attach';
        }

        return 'detach';
    }
}
