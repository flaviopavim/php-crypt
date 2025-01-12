<?php
/**
 * PHP Cipher and String Manipulation Library
 *
 * This script contains a collection of functions for text encryption, 
 * encoding, and manipulation. Below is an explanation of each function:
 * 
 * 1. **cesar($string, $level)**
 *    - Implements a basic Caesar Cipher.
 *    - Shifts characters in the string by a specified level ($level).
 *    - Works only with the characters in the matrices a-z, A-Z, 0-9.
 *    - Non-alphabetic characters remain unchanged.
 *    - Usage: Encrypt and decrypt strings with a fixed shift.
 * 
 * 2. **charval($c)**
 *    - Assigns a numeric value to a given character.
 *    - For letters, values are 1-26 (a-z).
 *    - For numbers, the value is the number itself.
 *    - Non-alphanumeric characters return 0.
 *    - Usage: Used as a helper for the Vigenère Cipher.
 * 
 * 3. **viginere($string, $key, $reverse = false)**
 *    - Implements the Vigenère Cipher.
 *    - Encrypts a string using a key, where each character in the key 
 *      determines the shift for the corresponding character in the string.
 *    - The reverse parameter allows decryption.
 *    - Usage: Securely encode and decode strings using a keyword-based cipher.
 * 
 * 4. **sh($s, $l = 3)**
 *    - Shuffles a string by grouping its characters based on the level ($l).
 *    - Rearranges characters into grouped patterns.
 *    - Usage: Scrambles a string for obfuscation purposes.
 * 
 * 5. **unsh($s, $l = 3)**
 *    - Reverses the operation of the `sh` function.
 *    - Restores a shuffled string to its original order.
 *    - Usage: Unscrambles strings shuffled by the `sh` function.
 * 
 * Example:
 * ```php
 * $txt = "Hello World";
 * $level = 3;
 * $key = "Key123";
 * 
 * // Caesar Cipher
 * $encoded = cesar($txt, $level);
 * $decoded = cesar($encoded, -$level);
 * 
 * // Vigenère Cipher
 * $vigenereEncoded = viginere($txt, $key);
 * $vigenereDecoded = viginere($vigenereEncoded, $key, true);
 * 
 * // Shuffle and Unshuffle
 * $shuffled = sh($txt, $level);
 * $unshuffled = unsh($shuffled, $level);
 * ```
 */

function caesar($string, $level) {
    /*
     * 'Caesar Cipher'
     * Code created by: Flávio Pavim
     * 
     * This function is a basic adaptation of the Caesar cipher.
     * It only works with characters from the arrays a-z, A-Z, 0-9.
     * 
     * Tip: Use base64 or some type of encoding that includes the same characters as this cipher
     * to make decoding increasingly difficult.
     */
    
    $m[] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLMNOPQRSTUVWXYZ'; // Repeated uppercase alphabet
    $m[] = 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz'; // Repeated lowercase alphabet
    $m[] = '01234567890123456789'; // Repeated digits
    $r = '';
    for ($a = 0; $a < 2; $a++) { // Loops 3 times to process each character set in $m
        if (!empty($r)) { // If it's not the first loop, converts the output to a string and resets $r
            $string = $r;
            $r = '';
        }
        for ($i = 0; $i < strlen($string); $i++) { // Iterates through each character
            $posm = strpos($m[$a], $string[$i]); // Finds the position of the character in $m
            if (is_numeric($posm)) { // If the character exists and is numeric
                $s = $posm + $level;
                if ($s <= 0) { // Handles negative shift values
                    $n = 26; // Number of letters in the alphabet for the first two arrays
                    if ($a == 2) {
                        $n = 10; // Only 10 numbers for the third array
                    }
                    $s = $s + $n; // Adjusts for the negative shift
                }
                $r .= $m[$a][$s]; // Substitutes the character
            } else {
                $r .= $string[$i]; // Keeps the original character if not found in $m
            }
        }
    }
    return $r;
}

function charval($c) {
    /*
     * Function to assign a value to each letter used in the key
     */
    if (is_numeric($c)) { // If it's a number
        return $c;
    }
    $m = 'abcdefghijklmnopqrstuvwxyz'; // Lowercase alphabet
    $s = strpos($m, strtolower($c)); // Finds the position of the character
    if (is_numeric($s)) {
        return $s + 1; // Returns the position + 1
    }
    return 0; // Default return if the character is not found
}

function vigenere($string, $key = '123', $reverse = false) {
    /*
     * Vigenère Cipher
     */
    $r = '';
    // $key = base64_encode($key); // Uncomment to encode the key
    $lk = strlen($key);
    for ($i = 0; $i < strlen($string); $i++) {
        $iactual = $i;
        while ($iactual >= $lk) {
            $iactual = $iactual - $lk;
        }
        if ($reverse == true) { // Decoding mode
            $r .= caesar($string[$i], -charval($key[$iactual]));
        } else { // Encoding mode
            $r .= caesar($string[$i], charval($key[$iactual]));
        }
    }
    return $r;
}

function sh($s, $l = 3) {
    /*
     * Shuffles the characters of a string
     * $l defines the grouping size
     */
    for ($i = 0; $i < strlen($s); $i++) {
        if (!isset($g) or $g == $l) { // Resets grouping counter
            $g = 0;
        }
        $a[$g][] = $s[$i]; // Groups characters
        $g++;
    }
    $s = '';
    foreach ($a as $f) { // Reorders the characters
        foreach ($f as $v) {
            $s .= $v;
        }
    }
    return $s;
}

function unsh($s, $l = 3) {
    /*
     * Reverts the shuffling process
     */
    $c = $a = 0;
    for ($i = 0; $i < strlen($s); $i++) {
        $arr[$c] = $s[$i];
        if ($c < (strlen($s) - $l)) {
            $c += $l; // Skips by group size
        } else {
            $a++;
            $c = $a;
        }
    }
    $r = '';
    for ($i = 0; $i < strlen($s); $i++) {
        $r .= $arr[$i];
    }
    return $r;
}

// Outputs for all functions

$txt = "Hello World";
$level = 3;
$key = "Key123";

// Outputs for Caesar Cipher
echo "=== Caesar Cipher ===<br>";
echo "Original Message: " . $txt . "<br>";
$cesarEncoded = caesar($txt, $level);
echo "Encoded (Level {$level}): " . $cesarEncoded . "<br>";
$cesarDecoded = caesar($cesarEncoded, -$level);
echo "Decoded: " . $cesarDecoded . "<br><br>";

// Outputs for Vigenère Cipher
echo "=== Vigenère Cipher ===<br>";
echo "Original Message: " . $txt . "<br>";
echo "Key: " . $key . "<br>";
$vigenereEncoded = vigenere($txt, $key);
echo "Encoded: " . $vigenereEncoded . "<br>";
$vigenereDecoded = vigenere($vigenereEncoded, $key, true);
echo "Decoded: " . $vigenereDecoded . "<br><br>";

// Outputs for Shuffle
echo "=== Shuffle ===<br>";
echo "Original Message: " . $txt . "<br>";
$shuffled = sh($txt, $level);
echo "Shuffled (Level {$level}): " . $shuffled . "<br>";
$unshuffled = unsh($shuffled, $level);
echo "Unshuffled: " . $unshuffled . "<br><br>";

// Outputs for charval
echo "=== Character Value ===<br>";
$char = 'K';
echo "Character: " . $char . "<br>";
$charValue = charval($char);
echo "Value: " . $charValue . "<br>";
$num = '5';
echo "Character: " . $num . "<br>";
$numValue = charval($num);
echo "Value: " . $numValue . "<br><br>";