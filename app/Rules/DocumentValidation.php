<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DocumentValidation implements Rule
{
    private $type;
    private $message;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function passes($attribute, $value)
    {
        $value = preg_replace('/[^0-9]/', '', (string) $value);

        if ($this->type === 'CPF') {
            return $this->validaCPF($value);
        }

        if ($this->type === 'CNPJ') {
            return $this->validaCNPJ($value);
        }

        return false;
    }

    public function message()
    {
        return "O {$this->type} informado não é válido.";
    }

    private function validaCNPJ($cnpj)
    {
        if (strlen($cnpj) != 14) {
            $this->message = 'CNPJ deve conter 14 dígitos.';
            return false;
        }

        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            $this->message = 'CNPJ inválido.';
            return false;
        }

        $soma = 0;
        for ($i = 0, $j = 5; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
            $this->message = 'CNPJ inválido.';
            return false;
        }

        $soma = 0;
        for ($i = 0, $j = 6; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;

        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }

    private function validaCPF($cpf)
    {
        if (strlen($cpf) != 11) {
            $this->message = 'CPF deve conter 11 dígitos.';
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $this->message = 'CPF inválido.';
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                $this->message = 'CPF inválido.';
                return false;
            }
        }
        return true;
    }
}
