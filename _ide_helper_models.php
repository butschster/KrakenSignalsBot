<?php
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Entities{
/**
 * App\Entities\Order
 *
 * @property int $id
 * @property string $txid
 * @property string $status
 * @property int $alert_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\Alert $alert
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\OrderDescription[] $descriptions
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order open()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereAlertId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereTxid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Order whereUpdatedAt($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Entities{
/**
 * App\Entities\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Entities{
/**
 * App\Entities\OrderDescription
 *
 * @property int $id
 * @property int $order_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\OrderDescription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\OrderDescription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\OrderDescription whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\OrderDescription whereUpdatedAt($value)
 */
	class OrderDescription extends \Eloquent {}
}

namespace App\Entities{
/**
 * App\Entities\Balance
 *
 * @property int $id
 * @property string $currency
 * @property float $amount
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Balance lastBalance()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Balance whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Balance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Balance whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Balance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Balance whereUpdatedAt($value)
 */
	class Balance extends \Eloquent {}
}

namespace App\Entities{
/**
 * App\Entities\Alert
 *
 * @property int $id
 * @property string $message_id
 * @property string $date
 * @property string $pair
 * @property string $type
 * @property float $volume
 * @property string $status
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Entities\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Alert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Alert whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Alert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Alert whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Alert wherePair($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Alert whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Alert whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Alert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Alert whereVolume($value)
 */
	class Alert extends \Eloquent {}
}

namespace App\Entities{
/**
 * App\Entities\Log
 *
 * @property int $id
 * @property string $message
 * @property string $type
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Log whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Log whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Log whereUpdatedAt($value)
 */
	class Log extends \Eloquent {}
}

