<?php


namespace User\Validation;


class PasswordRules
{
    /**
     * Callback method for change_password validation rule
     *
     * @param string $pwd = The previous password
     * @param string $user = The username
     * @return boolean = Whether or not the combination is correct
     */
    public function old_password_check($pwd, $user)
    {
        return (new \User\Models\User_model())->check_password_name($user, $pwd);
    }
}