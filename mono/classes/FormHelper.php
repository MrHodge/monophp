<?php

namespace mono\classes;

class FormHelper
{


    /**
     *
     * @var array
     */
    private static $validationErrors = [];

    /**
     *
     * @var array
     */
    private static $validationErrorParents = [];

    /**
     *
     * @var string
     */
    private static $errorListLayout = '<li>%s</li>';

    /**
     * @var string
     */
    private static $prependErrorList = '<ul style="text-align: left; width: 70%;margin: auto;padding-top:10px;">';

    /**
     * @var string
     */
    private static $appendErrorList = '</ul>';


    /**
     * This function allows you to validate form input.
     *
     * For more information on how to use this please visit: <a href="https://www.venomservices.com/venomframework/kb/FormHelper#validate">https://www.venomservices.com/venomframework/kb/FormHelper#validate</a>
     *
     * @param array $toCheck
     * @return bool
     */
    public static function validate($toCheck)
    {
        $errorSrc = debug_backtrace()[0];
        $passed = true;
        foreach ($toCheck as $input => $options) {
            $selfPassed = true;
            $name = isset($options['name']) ? $options['name'] : $input;

            $inputValue = Input::get($input);
            if(empty($inputValue)) { //Check headers
                $inputValue = Input::getHeader($input);
            }

            foreach ($options as $option => $optionValue) {
                $customMessage = null;
                if (is_array($optionValue)) {
                    if (isset($optionValue["custom_message"])) {
                        $customMessage = $optionValue["custom_message"];
                        unset($optionValue["custom_message"]);
                    }
                }
                if (isset($options['onlyif']) && is_array($options['onlyif'])) {
                    foreach ($options['onlyif'] as $key => $values) {
                        if (is_array($values)) {
                            foreach ($values as $value) {
                                switch ($value) {
                                    case 'exist':
                                    case 'exists':
                                        $val = Input::get($key);
                                        if(empty($val)) {
                                            $val = Input::getHeader($key);
                                        }
                                        if (strlen($val) <= 0) {
                                            break 4;
                                        }
                                        break;
                                    default:
                                        break;
                                }
                            }
                        }
                    }
                }
                switch (strtolower($option)) {
                    case 'required':
                        if(!is_array($optionValue) && !$optionValue && !strlen($inputValue)) break 2;
                        if(isset($inputValue["name"])) {
                            if (strlen($inputValue["name"]) <= 0) {
                                self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['required'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                                $passed = false;
                                $selfPassed = false;
                                break 2;
                            }
                        } else if (strlen($inputValue) <= 0) {
                            self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['required'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                            $passed = false;
                            $selfPassed = false;
                            break 2;
                        }
                        break;
                    case 'url' :
                        if (!filter_var($inputValue, FILTER_VALIDATE_URL)) {
                            self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['required'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                            $passed = false;
                            $selfPassed = false;
                            break 2;
                        }
                        break;
                    case 'email' :
                        if (!filter_var($inputValue, FILTER_VALIDATE_EMAIL)) {
                            self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['email'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                            $passed = false;
                            $selfPassed = false;
                            break 2;
                        }
                        break;
                    case 'alphabet' :
                        if (!preg_match('/^[a-z]+$/i', $inputValue)) {
                            self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['alphabet'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                            $passed = false;
                            $selfPassed = false;
                            break 2;
                        }
                        break;
                    case 'numeric' :
                        if (!preg_match('/^[0-9.]+$/i', $inputValue)) {
                            self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['numeric'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                            $passed = false;
                            $selfPassed = false;
                            break 2;
                        }
                        break;
                    case 'alphanumeric' :
                        if (!preg_match('/^[a-z0-9]+$/i', $inputValue)) {
                            self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['alphanumeric'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                            $passed = false;
                            $selfPassed = false;
                            break 2;
                        }
                        break;
                    case 'custom-regex' :
                        if (isset($optionValue['regex'])) {
                            if (!preg_match($optionValue['regex'], $inputValue)) {
                                self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['custom_regex'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                                $passed = false;
                                $selfPassed = false;
                                break 2;
                            }
                        } else {
                            Log::warning(sprintf(Mono()->getLang()['errors']['custom_regex'], $errorSrc['file'], $errorSrc['line']));
                        }
                        break;
                    case 'min-text' :
                        if (strlen($inputValue) < $optionValue) {
                            self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['min-text'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                            $passed = false;
                            $selfPassed = false;
                            break 2;
                        }
                        break;
                    case 'max-text' :
                        if (strlen($inputValue) > $optionValue) {
                            self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['max-text'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                            $passed = false;
                            $selfPassed = false;
                            break 2;
                        }
                        break;
                    case 'min-num' :
                        if ($inputValue < $optionValue) {
                            self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['min-num'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                            $passed = false;
                            $selfPassed = false;
                            break 2;
                        }
                        break;
                    case 'max-num' :
                        if ($inputValue > $optionValue) {
                            self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['max-num'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                            $passed = false;
                            $selfPassed = false;
                            break 2;
                        }
                        break;
                    case 'matches':
                        if ($inputValue !== Input::get($optionValue)) {
                            self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['password_doesnt_match'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                            $passed = false;
                            $selfPassed = false;
                            break 2;
                        }
                        break;
                    case 'csrftoken':
                        if ($inputValue !== Session::get(self::tokenName)) {
                            self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['csrftoken_doesnt_match'], $name, $optionValue) : sprintf($customMessage, $name, $optionValue));
                            $passed = false;
                            $selfPassed = false;
                            break 2;
                        }
                        break;
                    case 'select':
                        if (is_array($optionValue)) {
                            if (!array_key_exists($inputValue, $optionValue) && !in_array($inputValue, $optionValue)) {
                                $values = "";
                                $count = 1;
                                foreach ($optionValue as $val) {
                                    if ($count == count($optionValue)) {
                                        $values .= " or " . $val;
                                    } else {
                                        $values .= $val . ", ";
                                    }
                                    $count++;
                                }
                                self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['select'], $name, $values) : sprintf($customMessage, $name, $values));
                                $passed = false;
                                $selfPassed = false;
                                break 2;
                            }
                        }
                        break;
                    case 'date':
                        $f1 = \DateTime::createFromFormat("d/m/Y", $inputValue);
                        if ($f1->format("d/m/Y") != $inputValue) {
                            self::addValidationError(!($customMessage) ? sprintf(Mono()->getLang()['validation']['date'], $name) : sprintf($customMessage, $name));
                            $passed = false;
                            $selfPassed = false;
                            break 2;
                        }
                        break;
                    default :
                        break;
                }
            }
            if (!$selfPassed) {
                self::addValidationErrorParent($input);
            }
        }
        if (!$passed) {
            Variables::set("validationErrors", self::getValidationErrorParents());
            return false;
        } else {
            return true;
        }
    }

    /**
     * This function returns a list of errors using the HTML tag &lt;li&gt;&lt;/li&gt;
     *
     * @return string List of errors
     */
    public static function getValidationErrorList()
    {
        $string = self::$prependErrorList;
        foreach (self::$validationErrors as $error) {
            $string .= sprintf(self::$errorListLayout, $error);
        }
        return $string . self::$appendErrorList;
    }

    /**
     * This function returns a list of errors using the HTML tag &lt;li&gt;&lt;/li&gt;
     *
     * @return string List of errors
     */
    public static function getValidationErrorsJSON()
    {
        return json_encode(self::validationErrors);
    }

    /**
     * This function returns an array of validation errors.
     *
     * @return array
     */
    public static function getValidationErrors()
    {
        return self::$validationErrors;
    }

    /**
     * This function allows you to add a error to the validationErrors array.
     *
     * @param string $string
     */
    public static function addValidationError($string)
    {
        self::$validationErrors[] = $string;
    }

    /**
     * This function returns a randomly generated Cross Site Request Forgery token to use in a form.
     * Using this token is highly recommended for any important form you may be using in your application.
     * @param bool $regenerate
     * @return string CSRFToken
     */
    public static function getCSRFToken($regenerate = true)
    {
        if(Session::exists(tokenName) && !$regenerate){
            return Session::get(Mono()->getConfig()->getString("session_names.csrf_token"));
        }
        $token = Hash::unique();
        Log::info(sprintf(self::getLang()['log']['csrfToken_generated'], $token));
        return Session::put(Mono()->getConfig()->getString("session_names.csrf_token"), $token);
    }

    /**
     * This function allows you to check if the Cross Site Request Forgery token provided was actually generated by the server.
     * You would use this function to check the token after a form was submitted.
     *
     * @param string $token
     * @param bool $delete
     * @return bool
     */
    public static function verifyCSRFToken($token, $delete = true)
    {
        $currentToken = Session::get(Mono()->getConfig()->getString("session_names.csrf_token"));
        if (Session::exists(Mono()->getConfig()->getString("session_names.csrf_token")) && $token === $currentToken) {
            Log::info(sprintf(Mono()->getLang()['log']['csrfToken_verified'], $token));
            if($delete)
                Session::delete(Mono()->getConfig()->getString("session_names.csrf_token"));
            return true;
        } else {
            Log::warning(sprintf(Mono()->getLang()['log']['csrfToken_unverified'], $token, $currentToken));
            return false;
        }
    }

    /**
     * This function returns the error list layout.
     * Default to "<li>%s</li>"
     *
     * @return string
     */
    public static function getValidationErrorListLayout()
    {
        return self::$errorListLayout;
    }

    /**
     * This function allows you to set the error list layout.
     * Default to "<li>%s</li>"
     *
     * @param string $string
     */
    public static function setValidationErrorListLayout($string)
    {
        self::$errorListLayout = $string;
    }

    /**
     * This function returns the validation error parents.
     *
     * @return array
     */
    public static function getValidationErrorParents()
    {
        return self::$validationErrorParents;
    }

    /**
     * This function allows you to add a parent to the current array of validation error parents.
     *
     * @param string $parent
     */
    public static function addValidationErrorParent($parent)
    {
        array_push(self::$validationErrorParents, $parent);
    }

    /**
     * @return string
     */
    public static function getAppendErrorList()
    {
        return self::$appendErrorList;
    }

    /**
     * @param string $appendErrorList
     */
    public static function setAppendErrorList($appendErrorList)
    {
        self::$appendErrorList = $appendErrorList;
    }

    /**
     * @return string
     */
    public static function getPrependErrorList()
    {
        return self::$prependErrorList;
    }

    /**
     * @param string $prependErrorList
     */
    public static function setPrependErrorList($prependErrorList)
    {
        self::$prependErrorList = $prependErrorList;
    }

    /**
     * @return array
     */
    public static function getValidationStandard()
    {
        return [
            "validation_errors" => self::getValidationErrors(),
            "validation_error_parents" => self::getValidationErrorParents(),
        ];
    }

}