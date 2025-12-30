<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberRegistration extends Model
{
    protected $table = 'member_registrations';

    protected $fillable = [
        'last_name','company','primary_phone','mobile_phone',
        'primary_email','website','street','city','postal_code',
        'industry_id','dpd_location_id','annual_revenue',
        'number_of_employees','nib_siup','npwp_usaha',
        'ktp_pic','status','admin_note'
    ];

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function dpd()
    {
        return $this->belongsTo(DpdLocation::class, 'dpd_location_id');
    }
}
