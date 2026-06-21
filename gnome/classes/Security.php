<?php

namespace gnome\classes;

use gnome\classes\model\Indices;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

class Security extends DBConnection
{
    /**
     * Hold the Security singleton instance.
     *
     * Keep this property untyped to match the parent DBConnection class.
     *
     * @var Security|null
     */
    protected static $instance = null;

    /**
     * Current index permissions.
     *
     * The existing property spelling is retained because other files may
     * already reference userPremissions.
     *
     * @var array<string, mixed>
     */
    public $userPremissions = [];

    /**
     * Prevent construction outside this class.
     */
    private function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns the singleton Security instance.
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof Security)) {
            self::$instance = new Security();
        }

        return self::$instance;
    }

    /**
     * Requires a valid logged-in session.
     */
    public function isLoggedIn()
    {
        if (($_SESSION['loggedIn'] ?? false) !== true) {
            header('Location: /gnome/login');
            exit();
        }

        return $this;
    }

    /**
     * Returns whether the current user is a super administrator.
     */
    public function isSuperAdmin()
    {
        return in_array(
            'super_admin',
            $this->getSessionRoles(),
            true
        );
    }

    /**
     * Loads permissions for an index.
     *
     * Administrators and index owners receive explicit full permissions.
     * This ensures chained calls to hasRightAccess() continue to work.
     */
    public function indexPermission($indexId)
    {
        $indices = new Indices();

        $username = trim(
            (string) ($_SESSION['username'] ?? '')
        );

        if ($username === '') {
            $_SESSION['actionResponse'] =
                'Your session is missing a username.';

            header('Location: /gnome/login');
            exit();
        }

        /*
         * Site administrators have full index access.
         */
        if (
            in_array(
                'admin',
                $this->getSessionRoles(),
                true
            )
        ) {
            $this->userPremissions = $this->getFullPermissions(
                $indexId,
                false
            );

            return $this;
        }

        /*
         * Check index ownership before checking the separate permissions
         * table. Owners may not have a row in that table.
         */
        $owner = $indices->getIndexOwner($indexId);

        if (!is_array($owner)) {
            $owner = [];
        }

        /*
         * Supports likely existing aliases while preferring userName.
         */
        $ownerUsername = trim(
            (string) (
                $owner['userName']
                ?? $owner['username']
                ?? $owner['created_by']
                ?? ''
            )
        );

        if (
            $ownerUsername !== ''
            && strcasecmp($ownerUsername, $username) === 0
        ) {
            $this->userPremissions = $this->getFullPermissions(
                $indexId,
                true
            );

            return $this;
        }

        /*
         * Non-owner users must have a permissions-table record.
         */
        $permissions = $indices->canUserAccess(
            $indexId,
            $username
        );

        $this->userPremissions = is_array($permissions)
            ? $permissions
            : [];

        if (empty($this->userPremissions['count'])) {
            $_SESSION['actionResponse'] =
                'You do not have access.';

            header('Location: /gnome/indexer/indices.php');
            exit();
        }

        return $this;
    }

    /**
     * Requires a specific loaded index permission.
     *
     * Expected keys include:
     * can_read, can_write, can_admin, and is_owner.
     */
    public function hasRightAccess(
        $neededAccess,
        $failResponse = 'You do not have access.',
        $location = '/gnome/indexer/indices.php'
    ) {
        $permissionValue =
            $this->userPremissions[$neededAccess] ?? 0;

        /*
         * Permission values may be returned from MySQL as strings.
         */
        $hasAccess = (int) $permissionValue === 1;

        if (!$hasAccess) {
            $_SESSION['actionResponse'] = $failResponse;

            header("Location: {$location}");
            exit();
        }

        return $this;
    }

    /**
     * Returns whether the current user has an accepted role.
     *
     * Administrators automatically pass all role checks.
     *
     * @param string[] $acceptedRoles
     */
    public function roles($acceptedRoles = [])
    {
        $userRoles = $this->getSessionRoles();

        if (in_array('admin', $userRoles, true)) {
            return true;
        }

        foreach ($acceptedRoles as $acceptedRole) {
            if (
                in_array(
                    $acceptedRole,
                    $userRoles,
                    true
                )
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Placeholder retained for existing callers.
     */
    public function hasAccess()
    {
        return false;
    }

    /**
     * Returns full index permissions for administrators and owners.
     *
     * @return array<string, int|string>
     */
    private function getFullPermissions(
        $indexId,
        $isOwner
    ) {
        return [
            'count' => 1,
            'indices_id' => $indexId,
            'user_id' => (int) ($_SESSION['user_id'] ?? 0),
            'is_owner' => $isOwner ? 1 : 0,
            'can_read' => 1,
            'can_write' => 1,
            'can_admin' => 1,
        ];
    }

    /**
     * Safely reads roles stored in the current session.
     *
     * Supports roles stored either as a JSON string or as an array.
     *
     * @return string[]
     */
    private function getSessionRoles()
    {
        $roles = $_SESSION['roles'] ?? [];

        if (is_array($roles)) {
            return array_values(
                array_filter(
                    $roles,
                    static function ($role) {
                        return is_string($role)
                            && trim($role) !== '';
                    }
                )
            );
        }

        if (!is_string($roles) || trim($roles) === '') {
            return [];
        }

        $decodedRoles = json_decode($roles, true);

        if (!is_array($decodedRoles)) {
            return [];
        }

        return array_values(
            array_filter(
                $decodedRoles,
                static function ($role) {
                    return is_string($role)
                        && trim($role) !== '';
                }
            )
        );
    }
}

// Usage:
// $SECURITY = Security::getInstance();
// $SECURITY->isLoggedIn();