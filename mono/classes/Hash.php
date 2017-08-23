<?php

namespace mono\classes;

class Hash
{

    /**
     * This function makes a sha256 hash with an optional salt of the string provided.
     *
     * @param string $string
     * @param string $salt
     * @return string
     */
    public static function sha256($string, $salt = "")
    {
        return \hash('sha256', $string . $salt);
    }

    /**
     * @return string
     */
    public static function salt()
    {
        return uniqid(mt_rand(), true);;
    }

    /**
     * This function returns a unique sha256 hash.
     *
     * @return string
     */
    public static function unique()
    {
        return self::sha256(uniqid(), self::salt());
    }

    /**
     * This function utilizes the PHP password_hash function using BCrypt at an optional cost.
     * The default cost is 12.
     *
     * @param string $password
     * @param int $cost
     *
     * @return string
     */
    public static function password($password, $cost = 12)
    {
        return \password_hash($password, PASSWORD_BCRYPT, [
            'cost' => $cost
        ]);
    }

    /**
     * This function returns true if the password matches the hashed password. If the password doesn't match the hashed password false will be returned.
     *
     * @param string $password
     * @param string $hashedPassword
     *
     * @return boolean
     */
    public static function verifyPassword($password, $hashedPassword)
    {
        return \password_verify($password, $hashedPassword);
    }
}