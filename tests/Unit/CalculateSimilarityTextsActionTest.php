<?php

use App\Actions\CalculateSimilarityTextsAction;

it('calculates similarity for identical texts', function (): void {
    $action = new CalculateSimilarityTextsAction;

    $firstText = 'Hello World!';
    $secondText = 'Hello World!';

    expect($action->execute($firstText, $secondText))->toBe(100);
});

it('calculates similarity for completely different texts', function (): void {
    $action = new CalculateSimilarityTextsAction;

    $firstText = 'abc';
    $secondText = 'xyz';

    expect($action->execute($firstText, $secondText))->toBe(0);
});

it('calculates similarity for partial overlap', function (): void {
    $action = new CalculateSimilarityTextsAction;

    $firstText = 'Hello World!';
    $secondText = 'Hello there, World!';

    $result = $action->execute($firstText, $secondText);

    expect($result)->toBeGreaterThan(0)->toBeLessThan(100);
});

it('calculates similarity for texts with special characters and extra spaces', function (): void {
    $action = new CalculateSimilarityTextsAction;

    $firstText = 'Hello,    World!   ';
    $secondText = '  Hello World!';

    expect($action->execute($firstText, $secondText))->toBe(100);
});

it('calculates similarity when one text is empty', function (): void {
    $action = new CalculateSimilarityTextsAction;

    $firstText = 'Hello World!';
    $secondText = '';

    expect($action->execute($firstText, $secondText))->toBe(0);
});
