<?php

namespace App\Models;

use App\Models\Traits\ActiveTrait;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\ResetPasswordNotification;
use App\Observers\UserObserver;
use App\Traits\UtilsTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, HasPermissions, Notifiable, ActiveTrait, UtilsTrait;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'gender',
        'dob',
        'phone',
        'mobile',
        'address',
        'city',
        'state',
        'country_code',
        'zipcode',
        'avatar',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        User::observe(UserObserver::class);
    }

    public function business()
    {
        return $this->hasOne(Business::class);
    }

    protected function dob()
    {
        return Attribute::make(set: function ($value) {
            $this->attributes['date_of_birth'] = date('Y-m-d', strtotime($value));
        });
    }

    public function getNameAttribute($value)
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getAvatarUrlAttribute($value)
    {
        if ($this->avatar && Storage::exists($this->avatar)) {
            return Storage::url($this->avatar);
        } else {
            if ($this->gender == 'female') {
                return asset('images/avatar2.png');
            } else {
                return asset('images/avatar.png');
            }
        }
        return null;
    }

    public function setAvatarAttribute($value)
    {
        $attribute_name = 'avatar';

        $old_image = $this->{$attribute_name};

        // Path
        $destination_path = 'users';

        // If the image was erased
        if (empty($value)) {
            // delete the image from disk
            Storage::delete($this->{$attribute_name});

            $this->attributes[$attribute_name] = null;

            return false;
        }

        // If laravel request->file('filename') resource OR base64 was sent, store it in the db
        try {
            if ($value instanceof UploadedFile) {

                // Get file extension
                $extension = $value->getClientOriginalExtension();
                if (empty($extension)) {
                    $extension = 'jpg';
                }

                // Image default sizes
                $width = 300;
                $height = 300;

                // Make the image
                $image = Image::make($value)->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode($extension, 100);

                $filename = md5(microtime(true)) . '.' . $extension;

                // Store the image on disk.
                Storage::put($destination_path . '/' . $filename, $image->stream());

                $this->attributes[$attribute_name] = $destination_path . '/' . $filename;

                if ($old_image && Storage::exists($old_image)) {
                    Storage::delete($old_image);
                }
            } else {
                // Retrieve current value without upload a new file.
                if (!Str::startsWith($value, $destination_path)) {
                    $value = $destination_path . last(explode($destination_path, $value));
                }
                $this->attributes[$attribute_name] = $value;
            }
        } catch (\Exception $e) {
            alert_message($e->getMessage());
            $this->attributes[$attribute_name] = null;
            return false;
        }
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification($deal = null)
    {
        $this->notify(new EmailVerificationNotification($deal));
    }
}
