<?php
declare(strict_types=1);
namespace Modules\Accounting\Application\Services;
final class MoneyMath { public static function add(string $a,string $b): string { return bcadd($a,$b,6);} public static function sub(string $a,string $b): string { return bcsub($a,$b,6);} public static function mul(string $a,string $b): string { return bcmul($a,$b,6);} public static function div(string $a,string $b): string { return bcdiv($a,$b,6);} public static function cmp(string $a,string $b): int { return bccomp($a,$b,6);} }

