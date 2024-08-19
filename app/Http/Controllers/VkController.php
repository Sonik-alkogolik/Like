<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VkController extends Controller
{
    public function getGroups(Request $request)
    {
        $userId = $request->input('user_id');
        $accessToken = $request->input('access_token');
        $version = $request->input('v');
        $extended = $request->input('extended');

        $response = Http::get('https://api.vk.com/method/groups.get', [
            'user_id' => $userId,
            'access_token' => $accessToken,
            'v' => $version,
            'extended' => $extended,
        ]);

        return response()->json($response->json());
    }

    // public function checkGroup(Request $request)
    // {
    //     $groupId = $request->input('group_id');
    //     $userId = $request->input('user_id');
    //     $accessToken = $request->input('access_token');
    //     $version = $request->input('v');
    //     $extended = $request->input('extended');

    //     $response = Http::get('https://api.vk.com/method/groups.get', [
    //         'user_id' => $userId,
    //         'access_token' => $accessToken,
    //         'v' => $version,
    //         'extended' => $extended,
    //     ]);
     
    //     $groups = $response->json()['response']['items'];
    //     $groupExists = collect($groups)->contains('id', $groupId);

    //     return response()->json([
    //         'exists' => $groupExists
    //     ]);
    // }

 

    public function getGroupsAndCheckGroup(Request $request)
{
    \Log::info('Request data:', $request->all());

    $userId = $request->input('user_id');
    $accessToken = $request->input('access_token');
    $version = $request->input('v');
    $extended = $request->input('extended');
    $groupId = $request->input('group_id');

    $response = Http::get('https://api.vk.com/method/groups.get', [
        'user_id' => $userId,
        'access_token' => $accessToken,
        'v' => $version,
        'extended' => $extended,
    ]);

    \Log::info('VK API response:', $response->json());

    if (isset($response['response']['items'])) {
        $groups = $response['response']['items'];
        $groupExists = collect($groups)->contains('id', (int)$groupId);

        $groupsList = collect($groups)->map(function($group) {
            return 'Screen name: ' . $group['screen_name'] . ', ID: ' . $group['id'];
        })->toArray();

        return response()->json([
            'exists' => $groupExists,
            'message' => $groupExists 
                ? "Группа с ID {$groupId} существует в вашем списке групп."
                : "Группа с ID {$groupId} не найдена в вашем списке групп.",
            'groups' => $groupsList
        ]);
    } else {
        return response()->json(['error' => 'Failed to fetch groups or invalid response from VK API']);
    }
}
// public function saveGroup(Request $request)
// {
//     // Получаем данные из запроса
//     $groupLink = $request->input('group_link');
//     $accessToken = $request->input('access_token');
//     $version = $request->input('v');
//     $userId = $request->input('user_id');
    
//     // Выводим данные в вёрстку
//     return response()->json([
//         'group_link' => $groupLink,
//         'access_token' => $accessToken,
//         'version' => $version,
//         'user_id' => $userId
//     ]);
// }


// public function saveGroup(Request $request)
// {
//     $groupLink = $request->input('group_link');
//     $accessToken = $request->input('access_token');
//     $version = $request->input('v');
//     $userId = $request->input('user_id');

//     // Логируем полученные параметры
//     Log::info('Received request data:', [
//         'group_link' => $groupLink,
//         'access_token' => $accessToken,
//         'version' => $version,
//         'user_id' => $userId,
//     ]);

//     if (!$groupLink || !$accessToken || !$version || !$userId) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Отсутствуют необходимые параметры.',
//             'data' => [
//                 'group_link' => $groupLink,
//                 'access_token' => $accessToken,
//                 'version' => $version,
//                 'user_id' => $userId,
//             ],
//         ]);
//     }

//     // Извлекаем имя группы из ссылки
//     $groupName = parse_url($groupLink, PHP_URL_PATH);
//     $groupName = trim($groupName, '/');

//     if (!$groupName) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Не удалось извлечь имя группы из ссылки.',
//             'data' => [
//                 'group_link' => $groupLink,
//                 'access_token' => $accessToken,
//                 'version' => $version,
//                 'user_id' => $userId,
//                 'group_name' => $groupName,
//             ],
//         ]);
//     }

//     Log::info('Formatted group name:', ['group_name' => $groupName]);

//     $response = Http::get('https://api.vk.com/method/groups.getById', [
//         'group_ids' => $groupName,
//         'access_token' => $accessToken,
//         'v' => $version,
//     ]);

//     // Логируем полный ответ
//     Log::info('VK API response:', $response->json());

//     if ($response->successful()) {
//         $data = $response->json();
//         $groupId = $data['response'][0]['id'] ?? null;

//         return response()->json([
//             'success' => true,
//             'group_id' => $groupId,
//             'group_link' => $groupLink,
//             'access_token' => $accessToken,
//             'version' => $version,
//             'data' => $data,
//         ]);
//     } else {
//         return response()->json([
//             'success' => false,
//             'message' => 'Ошибка запроса к API VK.',
//             'data' => $response->json(),
//         ]);
//     }
// }
    
// public function saveGroup(Request $request)
// {
//     $groupLink = $request->input('group_link');
//     $accessToken = $request->input('access_token');
//     $version = $request->input('v');
//     $userId = $request->input('user_id');

//     // Логируем полученные параметры
//     Log::info('Received request data:', [
//         'group_link' => $groupLink,
//         'access_token' => $accessToken,
//         'version' => $version,
//         'user_id' => $userId,
//     ]);

//     if (!$groupLink || !$accessToken || !$version || !$userId) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Отсутствуют необходимые параметры.',
//             'data' => [
//                 'group_link' => $groupLink,
//                 'access_token' => $accessToken,
//                 'version' => $version,
//                 'user_id' => $userId,
//             ],
//         ]);
//     }

//     // Извлекаем имя группы из ссылки
//     $groupName = parse_url($groupLink, PHP_URL_PATH);
//     $groupName = trim($groupName, '/');

//     if (!$groupName) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Не удалось извлечь имя группы из ссылки.',
//             'data' => [
//                 'group_link' => $groupLink,
//                 'access_token' => $accessToken,
//                 'version' => $version,
//                 'user_id' => $userId,
//                 'group_name' => $groupName,
//             ],
//         ]);
//     }

//     Log::info('Formatted group name:', ['group_name' => $groupName]);

//     $response = Http::get('https://api.vk.com/method/groups.getById', [
//         'group_ids' => $groupName,
//         'access_token' => $accessToken,
//         'v' => $version,
//     ]);

//     // Логируем полный ответ
//     Log::info('VK API response:', $response->json());

//     if ($response->successful()) {
//         $data = $response->json();

//         // Извлечение данных группы из ответа
//         $groupId = $data['response']['groups'][0]['id'] ?? null;
//         $groupName = $data['response']['groups'][0]['name'] ?? null;

//         // Логируем извлечённые данные
//         Log::info('Extracted group data:', [
//             'group_id' => $groupId,
//             'group_name' => $groupName,
//             'group_link' => $groupLink,
//         ]);

//         return response()->json([
//             'success' => true,
//             'group_id' => $groupId,
//             'group_name' => $groupName,
//             'group_link' => $groupLink,
//             'data' => $data,
//         ]);
//     } else {
//         return response()->json([
//             'success' => false,
//             'message' => 'Ошибка запроса к API VK.',
//             'data' => $response->json(),
//         ]);
//     }
// }

public function saveGroup(Request $request)
{
    $groupLink = $request->input('group_link');
    $accessToken = $request->input('access_token');
    $version = $request->input('v');
    $userId = $request->input('user_id');

    // Логируем полученные параметры
    Log::info('Received request data:', [
        'group_link' => $groupLink,
        'access_token' => $accessToken,
        'version' => $version,
        'user_id' => $userId,
    ]);

    if (!$groupLink || !$accessToken || !$version || !$userId) {
        return response()->json([
            'success' => false,
            'message' => 'Отсутствуют необходимые параметры.',
            'data' => [
                'group_link' => $groupLink,
                'access_token' => $accessToken,
                'version' => $version,
                'user_id' => $userId,
            ],
        ]);
    }

    // Извлекаем имя группы из ссылки
    $groupName = parse_url($groupLink, PHP_URL_PATH);
    $groupName = trim($groupName, '/');

    if (!$groupName) {
        return response()->json([
            'success' => false,
            'message' => 'Не удалось извлечь имя группы из ссылки.',
            'data' => [
                'group_link' => $groupLink,
                'access_token' => $accessToken,
                'version' => $version,
                'user_id' => $userId,
                'group_name' => $groupName,
            ],
        ]);
    }

    Log::info('Formatted group name:', ['group_name' => $groupName]);

    $response = Http::get('https://api.vk.com/method/groups.getById', [
        'group_ids' => $groupName,
        'access_token' => $accessToken,
        'v' => $version,
    ]);

    // Логируем полный ответ
    Log::info('VK API response:', $response->json());

    if ($response->successful()) {
        $data = $response->json();
        $groupId = $data['response'][0]['id'] ?? null;
        $groupName = $data['response'][0]['name'] ?? null;

        // Логируем извлечённые данные
        Log::info('Extracted group data:', [
            'group_id' => $groupId,
            'group_name' => $groupName,
            'group_link' => $groupLink,
        ]);

        // Сохраняем ID группы, имя и ссылку в базу данных
        DB::table('user_groups')->updateOrInsert(
            ['user_id' => $userId, 'group_id' => $groupId],
            ['user_id' => $userId, 'group_id' => $groupId, 'group_name' => $groupName, 'group_link' => $groupLink]
        );

        return response()->json([
            'success' => true,
            'group_id' => $groupId,
            'group_name' => $groupName,
            'group_link' => $groupLink,
            'data' => $data,
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Ошибка запроса к API VK.',
            'data' => $response->json(),
        ]);
    }
}


public function showGroup()
{
    $group = DB::table('user_groups')->where('user_id', auth()->id())->first();

    return view('vk.vk-user', [
        'group_id' => $group->group_id ?? null,
        'group_link' => $group->group_link ?? null,
    ]);
}

}
