<?php

declare(strict_types=1);

namespace Modules\Authentication\Http\Support;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;

final class FormRequestValidator
{
    private static ?Factory $factory = null;

    /**
     * @param array<string, mixed>       $input
     * @param array<string, list<string>> $rules
     *
     * @return array<string, mixed>
     */
    public static function validate(array $input, array $rules): array
    {
        $validator = self::factory()->make($input, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    private static function factory(): Factory
    {
        if (self::$factory === null) {
            $translator = new Translator(new ArrayLoader(), 'en');
            self::$factory = new Factory($translator);
        }

        return self::$factory;
    }
}
