<?php

// namespace App\Controllers;

// use CodeIgniter\API\ResponseTrait;
// use CodeIgniter\HTTP\ResponseInterface;
// use App\Models\UserModel;
// use \Firebase\JWT\JWT;

// class AuthController extends BaseController
// {
//     use ResponseTrait;

//     protected $userModel;

//     public function __construct()
//     {
//         $this->userModel = new UserModel();
//     }

//     public function register()
//     {
//         // Lakukan validasi data
//         $rules = [
//             'email' => 'required|valid_email|is_unique[users.email]',
//             'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
//             'password' => 'required|min_length[6]',
//             'pass_confirm' => 'required|matches[password]'
//         ];

//         if (!$this->validate($rules)) {
//             return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
//         }

//         // Hash password
//         $password = $this->request->getVar('password');
//         $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

//         // Simpan data pengguna
//         $userData = [
//             'email' => $this->request->getVar('email'),
//             'username' => $this->request->getVar('username'),
//             'password' => $hashedPassword,
//             'role' => 'buyer' // Set default role to 'buyer'
//         ];

//         // Jika konfigurasi membutuhkan aktivasi, tambahkan langkah aktivasi di sini

//         $user = $this->userModel->save($userData);
//         if (!$user) {
//             return $this->fail('Failed to register user', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
//         }

//         return $this->respondCreated(['message' => 'User registered successfully']);
//     }


//     public function login()
//     {
//         $email = $this->request->getVar('email');
//         $username = $this->request->getVar('username');
//         $password = $this->request->getVar('password');

//         // Cari user berdasarkan email
//         $user = $this->userModel->where('email', $email)->first();

//         // Jika tidak ditemukan, cari user berdasarkan username
//         if (is_null($user) && !empty($username)) {
//             $user = $this->userModel->where('username', $username)->first();
//         }

//         // Jika user tidak ditemukan atau password tidak cocok, kirim respon error
//         if (is_null($user) || !password_verify($password, $user['password'])) {
//             return $this->respond(['error' => 'Invalid email/username or password.'], 401);
//         }

//         // Pembuatan token JWT dan respon berhasil
//         $key = getenv('JWT_SECRET');
//         $iat = time(); // current timestamp value
//         $exp = $iat + 3600;

//         $payload = array(
//             "iss" => "Issuer of the JWT",
//             "aud" => "Audience that the JWT",
//             "sub" => "Subject of the JWT",
//             "iat" => $iat, //Time the JWT issued at
//             "exp" => $exp, // Expiration time of token
//             "id" => $user['id'], // Add user ID to payload
//             "email" => $user['email'],
//         );

//         $token = JWT::encode($payload, $key, 'HS256');

//         $response = [
//             'message' => 'Login Succesful',
//             'token' => $token
//         ];

//         return $this->respond($response, 200);
//     }

//     public function loginadmin()
//     {
//         $email = $this->request->getVar('email');
//         $username = $this->request->getVar('username');
//         $password = $this->request->getVar('password');

//         // Cari user berdasarkan email
//         $user = $this->userModel->where('email', $email)->first();

//         // Jika tidak ditemukan, cari user berdasarkan username
//         if (is_null($user) && !empty($username)) {
//             $user = $this->userModel->where('username', $username)->first();
//         }

//         // Jika user tidak ditemukan atau password tidak cocok, kirim respon error
//         if (is_null($user) || !password_verify($password, $user['password'])) {
//             return $this->respond(['error' => 'Invalid email/username or password.'], 401);
//         }

//         // Check user role
//         if (!in_array($user['role'], ['staff', 'admin', 'super_admin'])) {
//             return $this->respond(['error' => 'You are not authorized to access this resource.'], 403);
//         }

//         // Pembuatan token JWT dan respon berhasil
//         $key = getenv('JWT_SECRET');
//         $iat = time(); // current timestamp value
//         $exp = $iat + 3600;

//         $payload = array(
//             "iss" => "Issuer of the JWT",
//             "aud" => "Audience that the JWT",
//             "sub" => "Subject of the JWT",
//             "iat" => $iat, // Time the JWT issued at
//             "exp" => $exp, // Expiration time of token
//             "id" => $user['id'], // Add user ID to payload
//             "email" => $user['email'],
//             "role" => $user['role'] // Add user role to payload
//         );

//         $token = JWT::encode($payload, $key, 'HS256');

//         $response = [
//             'message' => 'Login Successful',
//             'token' => $token
//         ];

//         return $this->respond($response, 200);
//     }


//     public function logout()
//     {
//         $this->userModel->logout();
//         return $this->respond(['message' => 'Logout successful']);
//     }
// }

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class AuthController extends BaseController
{
    use ResponseTrait;

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // public function register()
    // {
    //     $rules = [
    //         'email' => 'required|valid_email|is_unique[users.email]',
    //         'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
    //         'password' => 'required|min_length[6]',
    //         'pass_confirm' => 'required|matches[password]',
    //         'phone_number' => 'required|numeric|min_length[10]|max_length[15]' // New validation rule for phone number
    //     ];

    //     if (!$this->validate($rules)) {
    //         return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    //     }

    //     $password = $this->request->getVar('password');
    //     $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    //     $userData = [
    //         'email' => $this->request->getVar('email'),
    //         'username' => $this->request->getVar('username'),
    //         'password' => $hashedPassword,
    //         'phone_number' => $this->request->getVar('phone_number'), // Save phone number
    //         'role' => 'buyer'
    //     ];

    //     $userModel = new \App\Models\UserModel();

    //     if (!$userModel->save($userData)) {
    //         return $this->fail('Failed to register user', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    //     }

    //     return $this->respondCreated(['message' => 'User registered successfully']);
    // }


    public function login()
    {
        $loginIdentifier = $this->request->getVar('login'); // Can be email, username, or phone number
        $password = $this->request->getVar('password');

        // Search for user by email, username, or phone number
        $user = $this->userModel->where('email', $loginIdentifier)
            ->orWhere('username', $loginIdentifier)
            ->orWhere('phone_number', $loginIdentifier)
            ->first();

        if (is_null($user) || !password_verify($password, $user['password'])) {
            return $this->respond(['error' => 'Invalid login credentials.'], 401);
        }

        $key = getenv('JWT_SECRET');
        if (!$key) {
            return $this->fail('JWT secret not set', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $iat = time();
        $exp = $iat + 3600;

        $payload = [
            "iss" => "Issuer of the JWT",
            "aud" => "Audience of the JWT",
            "sub" => "Subject of the JWT",
            "iat" => $iat,
            "exp" => $exp,
            "id" => $user['id'],
            "email" => $user['email']
        ];

        try {
            $token = JWT::encode($payload, $key, 'HS256');
        } catch (\Exception $e) {
            return $this->fail('Failed to generate token', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response = [
            'message' => 'Login Successful',
            'token' => $token
        ];

        return $this->respond($response, 200);
    }

    public function loginadmin()
    {
        $loginIdentifier = $this->request->getVar('login'); // Can be email, username, or phone number
        $password = $this->request->getVar('password');

        // Search for user by email, username, or phone number
        $user = $this->userModel->where('email', $loginIdentifier)
            ->orWhere('username', $loginIdentifier)
            ->orWhere('phone_number', $loginIdentifier)
            ->first();

        if (is_null($user) || !password_verify($password, $user['password'])) {
            return $this->respond(['error' => 'Invalid login credentials.'], 401);
        }

        if (!in_array($user['role'], ['staff', 'admin', 'super_admin'])) {
            return $this->respond(['error' => 'You are not authorized to access this resource.'], 403);
        }

        $key = getenv('JWT_SECRET');
        if (!$key) {
            return $this->fail('JWT secret not set', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $iat = time();
        $exp = $iat + 3600;

        $payload = [
            "iss" => "Issuer of the JWT",
            "aud" => "Audience of the JWT",
            "sub" => "Subject of the JWT",
            "iat" => $iat,
            "exp" => $exp,
            "id" => $user['id'],
            "email" => $user['email'],
            "role" => $user['role']
        ];

        try {
            $token = JWT::encode($payload, $key, 'HS256');
        } catch (\Exception $e) {
            return $this->fail('Failed to generate token', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response = [
            'message' => 'Login Successful',
            'token' => $token
        ];

        return $this->respond($response, 200);
    }


    public function logout()
    {
        return $this->respond(['message' => 'Logout successful']);
    }

    public function register()
    {
        $rules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'phone_number' => 'required|numeric|min_length[10]|max_length[15]', // New validation rule for phone number
            'password' => 'required|min_length[6]',
            'pass_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $password = $this->request->getVar('password');
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $userData = [
            'email' => $this->request->getVar('email'),
            'username' => $this->request->getVar('username'),
            'phone_number' => $this->request->getVar('phone_number'),
            'password' => $hashedPassword,
            'role' => 'buyer'
        ];

        if (!$this->userModel->save($userData)) {
            return $this->fail('Failed to register user', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respondCreated(['message' => 'User registered successfully']);
    }

    //    public function login()
    // {
    //     $login = $this->request->getVar('login'); // Can be email, username, or phone number
    //     $password = $this->request->getVar('password');

    //     // Search for user by email, username, or phone number
    //     $user = $this->userModel->where('email', $login)
    //         ->orWhere('username', $login)
    //         ->orWhere('phone_number', $login)
    //         ->first();

    //     if (is_null($user) || !password_verify($password, $user['password'])) {
    //         return $this->respond(['error' => 'Invalid login credentials.'], 401);
    //     }

    //     $key = getenv('JWT_SECRET');
    //     if (!$key) {
    //         return $this->fail('JWT secret not set', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    //     }

    //     $iat = time();
    //     $exp = $iat + 3600;

    //     $payload = [
    //         "iss" => "Issuer of the JWT",
    //         "aud" => "Audience of the JWT",
    //         "sub" => "Subject of the JWT",
    //         "iat" => $iat,
    //         "exp" => $exp,
    //         "id" => $user['id'],
    //         "email" => $user['email']
    //     ];

    //     try {
    //         $token = JWT::encode($payload, $key, 'HS256');
    //     } catch (\Exception $e) {
    //         return $this->fail('Failed to generate token', ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    //     }

    //     $response = [
    //         'message' => 'Login Successful',
    //         'token' => $token
    //     ];

    //     return $this->respond($response, 200);
    // }
}


