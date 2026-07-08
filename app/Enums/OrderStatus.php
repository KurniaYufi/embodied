<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case AwaitingConfirmation = 'awaiting_confirmation';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => 'Pending Payment',
            self::AwaitingConfirmation => 'Awaiting Confirmation',
            self::Paid => 'Paid',
            self::Shipped => 'Shipped',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PendingPayment => 'zinc',
            self::AwaitingConfirmation => 'amber',
            self::Paid => 'blue',
            self::Shipped => 'sky',
            self::Completed => 'green',
            self::Cancelled => 'red',
        };
    }

    /**
     * @return array<int, self>
     */
    public static function options(): array
    {
        return self::cases();
    }
}
