# IntCoder

## About
This package can encode some integer in range [a; b] to string code with given length and alphabet. And can decode back.

## Usage
To install:
```
composer require mggflow/int-coder
```

Example:
```
$min = 3537;
$max = PHP_INT_MAX;

$alphabet = [
    'a', 'b', 'c', 'd',
    'e', 'f', 'g', 'h',
    'i', 'j', 'k', 'l',
    'm', 'n', 'o', 'p',
    'q', 'r', 's', 't',
    'u', 'v', 'w',
    'x', 'y', 'z',
    '0', '1', '2', '3',
    '4', '5', '6', '7',
    '8', '9'
];
$codeLength = 16;

$number = 931781;

$coder = new Int2Code($min, $max, $alphabet, $codeLength);
$decoder = new Code2Int($min, $max, $alphabet, $codeLength);

$code = $coder->encode($number);
echo "code: " . $code . "\n";
$decNumber = $decoder->decode($code);
echo "decoded number: " . $decNumber . "\n";
```

Expected output:
```
code: fkt2fs9qbwlams1u
decoded number: 931781
```