<?php

namespace Pterodactyl\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Pterodactyl\Exceptions\Model\DataValidationException;
use Illuminate\Database\Eloquent\Model as IlluminateModel;

abstract class Model extends IlluminateModel
{
    use HasFactory;

    /**
     * Set to true to return immutable Carbon date instances from the model.
     *
     * @var bool
     */
    protected $immutableDates = false;

    /**
     * Determines if the model should undergo data validation before it is saved
     * to the database.
     *
     * @var bool
     */
    protected $skipValidation = false;

    /**
     * The validator instance used by this model.
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * @var \Illuminate\Contracts\Validation\Factory
     */
    protected static $validatorFactory;

    /**
     * @var array
     */
    public static $validationRules = [];

    /**
     * Listen for the model saving event and fire off the validation
     * function before it is saved.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected static function boot()
    {
        parent::boot();

        static::$validatorFactory = Container::getInstance()->make(Factory::class);

        static::saving(function (Model $model) {
            if (!$model->validate()) {
                throw new DataValidationException($model->getValidator());
            }

            return true;
        });
    }

    /**
     * Returns the model key to use for route model binding. By default we'll
     * assume every model uses a UUID field for this. If the model does not have
     * a UUID and is using a different key it should be specified on the model
     * itself.
     *
     * You may also optionally override this on a per-route basis by declaring
     * the key name in the URL definition, like "{user:id}".
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Set the model to skip validation when saving.
     *
     * @return $this
     */
    public function skipValidation()
    {
        $this->skipValidation = true;

        return $this;
    }

    /**
     * Returns the validator instance used by this model.
     *
     * @return \Illuminate\Validation\Validator|\Illuminate\Contracts\Validation\Validator
     */
    public function getValidator()
    {
        $rules = $this->getKey() ? static::getRulesForUpdate($this) : static::getRules();

        return $this->validator ?: $this->validator = static::$validatorFactory->make(
            [],
            $rules,
            [],
            []
        );
    }

    /**
     * Returns the rules associated with this model.
     *
     * @return array
     */
    public static function getRules()
    {
        $rules = static::$validationRules;
        foreach ($rules as $key => &$rule) {
            $rule = is_array($rule) ? $rule : explode('|', $rule);
        }

        return $rules;
    }

    /**
     * Returns the rules for a specific field. If the field is not found an empty
     * array is returned.
     */
    public static function getRulesForField(string $field): array
    {
        return Arr::get(static::getRules(), $field) ?? [];
    }

    /**
     * Returns the rules associated with the model, specifically for updating the given model
     * rather than just creating it.
     *
     * @param \Illuminate\Database\Eloquent\Model|int|string $id
     *
     * @return array
     */
    public static function getRulesForUpdate($id, string $primaryKey = 'id')
    {
        if ($id instanceof Model) {
            [$primaryKey, $id] = [$id->getKeyName(), $id->getKey()];
        }

        $rules = static::getRules();
        foreach ($rules as $key => &$data) {
            // For each rule in a given field, iterate over it and confirm if the rule
            // is one for a unique field. If that is the case, append the ID of the current
            // working model so we don't run into errors due to the way that field validation
            // works.
            foreach ($data as &$datum) {
                if (!is_string($datum) || !Str::startsWith($datum, 'unique')) {
                    continue;
                }

                [, $args] = explode(':', $datum);
                $args = explode(',', $args);

                $datum = Rule::unique($args[0], $args[1] ?? $key)->ignore($id, $primaryKey)->__toString();
            }
        }

        return $rules;
    }

    /**
     * Determines if the model is in a valid state or not.
     *
     * @return bool
     */
    public function validate()
    {
        if ($this->skipValidation) {
            return true;
        }

        return $this->getValidator()->setData(
        // Trying to do self::toArray() here will leave out keys based on the whitelist/blacklist
        // for that model. Doing this will return all of the attributes in a format that can
        // properly be validated.
            $this->addCastAttributesToArray(
                $this->getAttributes(),
                $this->getMutatedAttributes()
            )
        )->passes();
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param mixed $value
     *
     * @return \Illuminate\Support\Carbon|\Carbon\CarbonImmutable
     */
    protected function asDateTime($value)
    {
        if (!$this->immutableDates) {
            return parent::asDateTime($value);
        }

        return parent::asDateTime($value)->toImmutable();
    }
}
