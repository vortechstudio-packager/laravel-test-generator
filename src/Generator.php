<?php

namespace Vortechstudio\LaravelTestGenerator;

use Faker\Factory;

class Generator
{
    protected \Faker\Generator $faker;

    protected $params;

    protected $cases;

    protected $rules;

    /**
     * Initiates the global parameters
     */
    public function __construct()
    {
        $this->faker = Factory::create();
        $this->cases = [];
    }

    /**
     * Initialize the params and rules and generates the test casesOK
     */
    public function generate(): ?array
    {
        return $this->generateCase();
    }

    /**
     * Generate the cases for testing
     *
     * @return array The generated cases
     */
    protected function generateCase(): array
    {
        $this->generateFailureCase();
        $this->generateSuccessCase();

        return $this->cases;
    }

    /**
     * Generate the success test case
     */
    protected function generateSuccessCase(): void
    {
        $case = [];
        $value = '';
        foreach ($this->params as $key => $val) {
            $case[$val] = $this->getValue(is_string($val) ? $val : strval($val), $this->rules[$key]);
        }

        $this->cases['success'] = $case;
    }

    /**
     * Get the value for the given field with the applied rules
     *
     * @param [array] $rules
     */
    protected function getValue(string $param, $rules): string
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }
        $value = '';

        switch ($rules) {
            case $this->isEmail($rules):
                $value = $this->faker->email;
                break;
            case $this->isCompanyName($rules, $param):
                $value = $this->faker->company;
                break;
            case $this->isAddress($rules, $param):
                $value = $this->faker->address;
                break;
            case $this->isName($rules, $param):
                $value = $this->faker->name;
                break;
            case $this->isStreetName($rules, $param):
                $value = $this->faker->streetName;
                break;
            case $this->isStreetAddress($rules, $param):
                $value = $this->faker->streetAddress;
                break;
            case $this->isCity($rules, $param):
                $value = $this->faker->city;
                break;
            case $this->isState($rules, $param):
                $value = $this->faker->state;
                break;
            case $this->isCountry($rules, $param):
                $value = $this->faker->country;
                break;
            case $this->isZip($rules, $param):
                $value = $this->faker->postcode;
                break;
            case $this->isLatitude($param):
                $value = $this->faker->latitude;
                break;
            case $this->isLongitude($param):
                $value = $this->faker->longitude;
                break;
            case $this->isPhone($param):
                $value = $this->faker->e164PhoneNumber;
                break;
            case $this->isBoolean($rules):
                $value = rand(0, 1);
                break;
            case $this->isDate($rules):
                $value = $this->faker->date;
                break;
            case $this->isDateFormat($rules):
                $format = array_values(array_filter($rules, function ($val) {
                    return preg_match('/^date_format/', $val);
                }));
                $format = str_replace('date_format:', '', $format[0]);
                $value = $this->faker->date($format);
                break;
        }

        return $value;
    }

    /**
     * Check whether email is applicable for the given field
     */
    protected function isEmail(array $rules): bool
    {
        return in_array('email', $rules);
    }

    /**
     * Check whether company name is applicable for the given field
     */
    protected function isCompanyName(array $rules, string $param): bool
    {
        return strpos('company', $param) !== false && in_array('string', $rules);
    }

    /**
     * Check whether address is applicable for the given field
     */
    protected function isAddress(array $rules, string $param): bool
    {
        return strpos('address', $param) !== false && in_array('string', $rules);
    }

    /**
     * Check whether name is applicable for the given field
     *
     * @param  string  $param
     */
    protected function isName(array $rules, $param): bool
    {
        return strpos('name', $param) !== false && in_array('string', $rules);
    }

    /**
     * Check whether stree name is applicable for the given field
     *
     * @param  array  $rules
     * @param  string  $param
     * @return bool
     */
    protected function isStreetName($rules, $param)
    {
        return strpos('street', $param) !== false && in_array('string', $rules);
    }

    /**
     * Check whether street address is applicable for the given field
     *
     * @param  array  $rules
     * @param  string  $param
     * @return bool
     */
    protected function isStreetAddress($rules, $param)
    {
        return strpos('street_address', $param) !== false && in_array('string', $rules);
    }

    /**
     * Check whether city is applicable for the given field
     *
     * @param  array  $rules
     * @param  string  $param
     * @return bool
     */
    protected function isCity($rules, $param)
    {
        return strpos('city', $param) !== false && in_array('string', $rules);
    }

    /**
     * Check whether state is applicable for the given field
     *
     * @param  array  $rules
     * @param  string  $param
     * @return bool
     */
    protected function isState($rules, $param)
    {
        return strpos('state', $param) !== false && in_array('string', $rules);
    }

    /**
     * Check whether country is applicable for the given field
     *
     * @param  array  $rules
     * @param  string  $param
     * @return bool
     */
    protected function isCountry($rules, $param)
    {
        return strpos('country', $param) !== false && in_array('string', $rules);
    }

    /**
     * Check whether zip is applicable for the given field
     *
     * @param  array  $rules
     * @param  string  $param
     * @return bool
     */
    protected function isZip($rules, $param)
    {
        return (strpos('zip', $param) !== false || strpos('pin', $param) !== false) && in_array('string', $rules);
    }

    /**
     * Check whether latitude is applicable for the given field
     *
     * @param  string  $param
     * @return bool
     */
    protected function isLatitude($param)
    {
        return strpos('latitude', $param) !== false;
    }

    /**
     * Check whether longitude is applicable for the given field
     *
     * @param  string  $param
     * @return bool
     */
    protected function isLongitude($param)
    {
        return strpos('longitude', $param) !== false;
    }

    /**
     * Check whether phone number is applicable for the given field
     *
     * @param  string  $param
     * @return bool
     */
    protected function isPhone($param)
    {
        return strpos('phone', $param) !== false || strpos('mobile', $param) !== false;
    }

    /**
     * Check whether boolean type is applicable for the given field
     *
     * @param  array  $rules
     * @return bool
     */
    protected function isBoolean($rules)
    {
        return in_array('boolean', $rules);
    }

    /**
     * Check whether date type is applicable for the given field
     *
     * @param  array  $rules
     * @return bool
     */
    protected function isDate($rules)
    {
        return in_array('date', $rules);
    }

    /**
     * Check whether date or time is applicable for the given field
     *
     * @param  array  $rules
     * @return bool
     */
    protected function isDateFormat($rules)
    {
        $format = array_filter($rules, function ($val) {
            return preg_match('/^date_format/', $val);
        });

        return count($format);
    }

    /**
     * Generate failure test case
     *
     * @return void
     */
    protected function generateFailureCase()
    {
        $this->cases['failure'] = array_fill_keys($this->params, '');
    }
}
