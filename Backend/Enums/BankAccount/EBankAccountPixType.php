<?php
namespace Backend\Enums\BankAccount;

enum EBankAccountPixType: string
{
  case CPF = 'cpf';
  case CNPJ = 'cnpj';
  case EMAIL = 'email';
  case PHONE = 'phone';
  case RANDOM = 'random';
  case ANOTHER = 'another';
}