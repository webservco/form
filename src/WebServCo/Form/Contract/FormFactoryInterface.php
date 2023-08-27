<?php

declare(strict_types=1);

namespace WebServCo\Form\Contract;

interface FormFactoryInterface
{
    public function createForm(): FormInterface;
}
