<?php declare(strict_types=1);

namespace ElioTest\Service;

use Symfony\Component\HttpFoundation\Request;

class ValidateFormService
{
    public function validateForm(Request $request, int $maxProductCount)
    {
        for ($i = 1; $i <= $maxProductCount; $i++)
        {
            if ($request->get("item{$i}") === '') {
                continue;
            }

            if (\is_string($request->get("item{$i}")) === false) {
                throw new \Exception('Product id is not a string');
            }

            if (\ctype_print($request->get("item{$i}")) === false) {
                throw new \Exception('Product id has invalid characters');
            }

            if (\ctype_print($request->get("qnt{$i}")) === false) {
                throw new \Exception('Product quantity has invalid characters');
            }

            if (\is_integer(intval($request->get("qnt{$i}"))) === false) {
                throw new \Exception('Product quantity is not an integer');
            }
        }
    }
}
