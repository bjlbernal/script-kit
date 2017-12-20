#!/usr/local/bin/php -q
<?php
$principle = null; // float value accepted
$apr = null; // in percents without the suffix
$compounded = null; // daily/monthly/annually
$validatedAnswer = false;

class poci
{
    public $questions = [
        "What is your principle?",
        "What is the APR? (Annual Percentage Rate)",
        "How is interest to be compounded? (daily, monthly, annually)"
    ];

    public function askQuestion($q)
    {
        echo $this->questions[$q] . "\n";
    }

    public function invalidInputError($validation)
    {
        if (!$validation) {
            echo "\nThe value you entered was not expected. Please try again.\n\n";
        }
    }

    public function validateInput($input, $expected)
    {
        $theType = getType($input);
    
        if ($theType == $expected) {
            return true;
        }

        echo "Value type given, $theType, was not the type $expected.\n";

        return false;
    }
    
    public function waitForInput()
    {
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        $value = trim($line);
    
        return $value;
    }
}

$POCI = new poci();

while (!$validatedAnswer) {
    $POCI->askQuestion(0);
    $principle = (double)$POCI->waitForInput();
    $validatedAnswer = $POCI->validateInput($principle, 'double');
    $POCI->invalidInputError($validatedAnswer);
}

$validatedAnswer = false;

while (!$validatedAnswer) {
    $POCI->askQuestion(1);
    $apr = (double)$POCI->waitForInput();
    $validatedAnswer = $POCI->validateInput($apr, 'double');
    $POCI->invalidInputError($validatedAnswer);
}

$validatedAnswer = false;

while (!$validatedAnswer) {
    $POCI->askQuestion(2);
    $compounded = $POCI->waitForInput();
    $validatedAnswer = $POCI->validateInput($compounded, 'string');

    if ($validatedAnswer) {
        $validatedAnswer = (in_array($compounded, ['daily', 'monthly', 'annually']));
    }

    $POCI->invalidInputError($validatedAnswer);
}

$dailyInterest = (($apr/100)/365);
$interestAccrued = 0;

switch ($compounded) {
case 'daily':
    $principleAndInterest = $principle;

    for ($d = 0; $d < 365; $d++) {
        $principleAndInterest *= 1 + $dailyInterest;
    }

    $interestAccrued = $principleAndInterest - $principle;

    break;
case 'monthly':
    $principleAndInterest = $principle;
    $monthlyInterest = 0;
    $modulos = 365/12;

    for ($d = 0; $d < 365; $d++) {
        $monthlyInterest += $principleAndInterest * $dailyInterest;

        if ($d%$modulos < 1) {
            $interestAccrued += $monthlyInterest;
            $principleAndInterest += $monthlyInterest;
            $monthlyInterest = 0;
        }
    }

    break;
case 'annually':
    $principleAndInterest = $principle;
    $annualInterest = 0;

    for ($d = 0; $d < 365; $d++) {
        $annualInterest += $principleAndInterest * $dailyInterest;
    }

    $interestAccrued += $annualInterest;
    $principleAndInterest += $annualInterest;
    break;
}

echo "\n";
echo "Principle: $principle\n";
echo "Accrued Interest: $interestAccrued ($dailyInterest compounded $compounded)\n";
echo "Total: $principleAndInterest\n";

