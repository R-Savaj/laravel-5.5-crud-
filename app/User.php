<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Validator;

class User extends Authenticatable {

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'age', 'date_of_birth', 'mobile', 'location'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public static function validate($data, $id) {
        $rule = array(
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'mobile' => 'required|numeric|min:10|digits_between:1,10',
            'email' => 'required|max:100|email|unique:users,email,' . $id . ',id',
            'age' => 'required|numeric|min:0',
            'location' => 'required|max:100',
            'date_of_birth' => 'required',
        );
        $messages = array(
            'required' => ':attribute field is required.',
            'max' => ':attribute may not be greater than :max characters.',
            'min' => ':attribute may not be less than :min.',
            'numeric' => ':attribute must be a number.',
        );
        $data = Validator::make($data, $rule, $messages);
        $data->setAttributeNames(array(
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'age' => 'Age',
            'date_of_birth' => 'Date of Birth',
            'location' => 'Location'
        ));
        return $data;
    }

}
