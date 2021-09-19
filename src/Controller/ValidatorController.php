<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ValidatorController extends AbstractController
{
    public function validator($data, $param): array {
        foreach ($param as $value) {
            if ($value === 'required') return $this->validatorRequired($data);
        }
        return [];
    }

    public function validatorRequired($data): array
    {
        $errors = [];
        foreach ($data as $key => $item) {
            if (strlen($item) < 1) {
                $errors += [
                    $key => 'This field is required'
                ];
            }
        }
        return $errors;
    }
}