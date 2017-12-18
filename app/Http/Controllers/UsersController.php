<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\User;
use ResponseManager;
use Exception;
use DB;
use Validator;

class UsersController extends Controller {

    /**
     * @author Rachna Savaj
     * 
     * Method name: index
     * This method is used for list of the users.
     *
     * @return  user list,Response code,message.
     * @exception throw if any error occur when getting user's list.
     */
    public function index() {
        try {
            $user = User::select('id', 'first_name', 'last_name', 'email', 'age', 'mobile', 'date_of_birth', 'location')->orderBy('updated_at', 'DESC')->get();
            if (count($user) > 0) {
                return Response()->json(ResponseManager::getResult($user, 200, ''));
            } else {
                return Response()->json(ResponseManager::getResult('', 200, 'Could not locate any user.'));
            }
        } catch (Exception $ex) {
            throw new Exception($ex);
        }
    }

    /**
     * @author Rachna Savaj
     * 
     * Method name: store
     * This method is used for create user.
     *
     * @param  {varchar} first name - first name of the user.
     * @param  {varchar} last name - last name of the user.
     * @return  Response code,message.
     * @exception throw if any error occur when creating user.
     */
    public function store(Request $request) {
        $result = DB::transaction(function () use ($request) {
                    try {
                        $input = $request->all();
                        $input['date_of_birth'] = strtotime($input['date_of_birth']) * 1000;

                        $validator = Validator::make($input, [
                                    'first_name' => 'required|max:100',
                                    'last_name' => 'required|max:100',
                                    'mobile' => 'required|numeric|digits_between:1,10',
                                    'email' => 'required|max:100|email|unique:users,email',
                                    'age' => 'required|numeric|min:0',
                                    'location' => 'required|max:100',
                                    'date_of_birth' => 'required',
                        ]);
                        if ($validator->fails()) {
                            Log::info($validator->messages());
                            $breakline = $validator->messages()->all();
                            $message = implode(',', $breakline);
                            $data['validationCheck'] = 1;
                            return Response()->json(ResponseManager::getError($data, 10, $message));
                        }
                        $user = User::create($input);
                        if (null != $user && "" != $user) {
                            return Response()->json(ResponseManager::getResult('', 200, 'User created successfully.'));
                        } else {
                            return Response()->json(ResponseManager::getError('', 10, 'Sorry,Could not create user.Please try again.'));
                        }
                    } catch (Exception $ex) {
                        throw new Exception($ex);
                    }
                });
        return $result;
    }

    /**
     * Display the specified user detail.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $result = DB::transaction(function () use ($id) {
                    try {
                        $user = User::select('id', 'first_name', 'last_name', 'email', 'age', 'mobile', 'date_of_birth', 'location')->find($id);
                        if (null != $user && "" != $user) {
                            $data['user'] = $user;
                            return Response()->json(ResponseManager::getResult($data, 200, ''));
                        } else {
                            return Response()->json(ResponseManager::getError('', 10, 'Sorry,could not show user detail.Please try again.'));
                        }
                    } catch (Exception $ex) {
                        throw new Exception($ex);
                    }
                });
        return $result;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $result = DB::transaction(function () use ($request, $id) {
                    try {
                        $input = $request->all();
                        $input['date_of_birth'] = strtotime($input['date_of_birth']) * 1000;
                        $validation = User::validate($input, $id);
                        if ($validation != null && $validation != "" && $validation->fails()) {
                            $breakline = $validation->messages()->all();
                            $message = implode(",", $breakline);
                            $data['validationCheck'] = 1;
                            return Response()->json(ResponseManager::getError($data, 10, $message));
                        }
                        unset($input['id']);
                        $user = User::where('id', $id)->update($input);
                        if (!$user) {
                            return Response()->json(ResponseManager::getError('', 10, 'Sorry,could not update user detail.Please try again.'));
                        } else {
                            return Response()->json(ResponseManager::getResult('', 200, 'User detail updated successfully.'));
                        }
                    } catch (Exception $ex) {
                        throw new Exception($ex);
                    }
                });
        return $result;
    }

}
