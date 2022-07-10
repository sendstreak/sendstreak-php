<?php

namespace Tinkermail\TinkermailPhp;

use JsonSerializable;

/**
 * Represents a Contact in Tinkermail.
 */
class Contact implements JsonSerializable
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $additionalData;

    public function __construct(string $email, array $additionalData = [])
    {
        if (!self::validateEmail($email)) {
            throw new \Exception("An invalid email address has been provided: '$email' is not a valid address.");
        }

        $this->email = $email;
        $this->additionalData = $additionalData;
    }

    /**
     * Returns the email address of the contact.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Sets the email address of the contact.
     * 
     * @throws Exception If an invalid email is provided.
     */
    public function setEmail(string $value): void
    {
        if (!self::validateEmail($value)) {
            throw new \Exception("An invalid email address has been provided: '$value' is not a valid address.");
        }

        $this->email = $value;
    }

    /**
     * Returns the additional information of the contact.
     */
    public function getData(string $key)
    {
        return array_key_exists($key, $this->additionalData) ? $this->additionalData[$key] : null;
    }

    /**
     * Sets additional information for the contact. These can be strings, numbers or booleans.
     * 
     * @throws Exception If the type of `$data` is invalid.
     */
    public function setData(string $key, $data): void
    {
        $this->additionalData[$key] = $data;
    }

    /**
     * Removes additional information from the contact.
     * 
     * @throws Exception If `$key` does not exist among the contact data.
     */
    public function removeData(string $key): void
    {
        if (!array_key_exists($key, $this->additionalData)) {
            throw new \Exception("Contact data with key '$key' does not exist.");
        }

        unset($this->additionalData[$key]);
    }

    public function jsonSerialize(): array
    {
        $array = [
            'email' => $this->email,
        ];

        if (!empty($this->additionalData)) {
            foreach ($this->additionalData as $key => $value) {
                $array[$key] = $value;
            }
        }

        return $array;
    }

    private static function validateEmail(string $email): bool
    {
        if (!$email || !preg_match("/.+@.+\..+/u", $email)) {
            return false;
        }

        return true;
    }
}
