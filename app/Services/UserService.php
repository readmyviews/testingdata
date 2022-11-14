<?php

namespace App\Services;

use App\Jobs\RegisterAdminEmailQueue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserService
{

    public function getUserObject()
    {
        return new User();
    }
    /**
     * Get All data
     *
     * @return object
     */
    public function getUserData()
    {
        return $this->getUserObject()->with('role');
    }
    /**
     * storeCountry function
     *
     * @param Request $request
     * @return object
     */
    public function storeUser(Request $request)
    {
        $data = $request->validated();
        $password = $this->generatePassword();
        $data['password'] = bcrypt($password);
        if ($request->hasFile('file')) {
            $data['avatar'] = Storage::disk('avatar')->put(config('params.upload.user_avatar'), $request->file);
        }
        $user = User::create($data);
        // Assign Roles to User
        $user->syncRoles($user->role->name);
        if (!in_array($user->user_role, config('params.admin_role_id'))) {
            $emailData['name'] = $user->first_name;
            $emailData['email'] = $user->email;
            $emailData['password'] = $password;
            $emailData['role'] = $user->role->name;
            $registrationEmailQueue = new RegisterAdminEmailQueue($emailData);
            dispatch($registrationEmailQueue)->delay(now()->addSeconds(6));
        }
        return $user;
    }
    /**
     * getProductAttribute function
     *
     * @param string $uuid
     * @return object
     */
    public function getUser(string $uuid)
    {
        return $this->getUserObject()->where('uuid', $uuid)->first();
    }
    /**
     * updateCountry function
     *
     * @param Request $request
     * @return void
     */
    public function updateUser(Request $request, $uuid)
    {
        $data = $request->validated();
        $user = $this->getUser($uuid);
        if ($request->hasFile('file')) {
            $data['avatar'] = Storage::disk('avatar')->put(config('params.upload.user_avatar'), $request->file);
            if ($user->avatar != '') {
                Storage::disk('avatar')->delete($user->avatar);
            }
        }
        $user->update($data);
        // Assign Roles to User
        $user->syncRoles($user->role->name);
        return $user;
    }
    /**
     * deletePermission function
     *
     * @param string $uuid
     * @return boolean
     */
    public function deleteUser(string $uuid)
    {
        $user = $this->getUser($uuid);
        $user->delete();
        return true;
    }
    /**
     * deleteMultipleUser function
     *
     * @param Request $request
     * @return boolean
     */
    public function deleteMultipleUser(Request $request)
    {
        $ids = explode(",", $request->get('ids'));
        User::destroy($ids);
        return true;
    }
    /**
     * generatePassword function
     *
     * @return string
     */
    public function generatePassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890@!#$%^&*_=+*';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    /**
     * getActiveCountries function
     *
     * @return array
     */
    public function getActiveCountries()
    {
        return (new CountryService())->getCountryData()->where('status', config('params.active'))->get();
    }
    /**
     * getActiveRoles function
     *
     * @return array
     */
    public function getActiveRoles()
    {
        return (new RoleService())->getRoleData()->where('status', config('params.active'))->get();
    }
}