<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\UserGroup;


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

    public function checkGroup(Request $request)
    {
        Log::info('Check Group Request Data:', $request->all());
    
        // Преобразуем ID группы к строке
        $groupId = (string) $request->input('group_id'); 
        $userId = $request->input('user_id');
        $accessToken = $request->input('access_token');
        $version = $request->input('v');
        $extended = $request->input('extended');
    
        Log::info("Fetching groups for user ID: $userId with group ID: $groupId");
    
        $response = Http::get('https://api.vk.com/method/groups.get', [
            'user_id' => $userId,
            'access_token' => $accessToken,
            'v' => $version,
            'extended' => $extended,
        ]);
    
        Log::info('VK API Response:', $response->json());
    
        if ($response->successful()) {
            $groups = collect($response->json()['response']['items'])->map(function($group) {
                Log::info('Group ID from VK:', ['id' => (string)$group['id']]); // Преобразуем ID к строке для сравнения
                return (string)$group['id'];
            });
    
            // Проверяем наличие groupId в списке групп
            $groupExists = $groups->contains($groupId);
    
            Log::info('Does group exist?', ['groupExists' => $groupExists, 'groupId' => $groupId, 'allGroupIds' => $groups]);
    
            return response()->json([
                'exists' => $groupExists
            ]);
        } else {
            Log::error('VK API Request failed:', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
    
            return response()->json([
                'error' => 'Failed to retrieve groups from VK API'
            ], 500);
        }
    }
    

 
 public function getGroupsAndCheckGroup(Request $request)
{
    Log::info('Request data:', $request->all());

    $vkUserId = $request->input('user_id');
    $accessToken = $request->input('access_token');
    $version = $request->input('v');
    $groupLink = $request->input('group_link');

    // Извлечь короткое имя группы из ссылки
    $groupShortName = parse_url($groupLink, PHP_URL_PATH);
    $groupShortName = ltrim($groupShortName, '/'); // Удалить начальный '/'

    // Получить числовой group_id по короткому имени
    $response = Http::get('https://api.vk.com/method/groups.getById', [
        'group_ids' => $groupShortName,
        'access_token' => $accessToken,
        'v' => $version,
    ]);

    Log::info('VK API response:', $response->json());

    if ($response->successful()) {
        $data = $response->json();

        if (isset($data['response']['groups'][0]['id'])) {
            $groupId = $data['response']['groups'][0]['id'];

            // Получаем user_id из vk_page_users
            $vkUser = DB::table('vk_page_users')->where('vk_user_id', $vkUserId)->first();

            if ($vkUser) {
                $userId = $vkUser->user_id;

                // Проверяем, существует ли уже такая запись
                $existingGroup = DB::table('user_groups')
                    ->where('user_id', $userId)
                    ->where('group_id', $groupId)
                    ->first();

                if (!$existingGroup) {
                    // Вставляем данные в таблицу user_groups
                    DB::table('user_groups')->insert([
                        'user_id' => $userId,
                        'group_id' => $groupId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    Log::info("Группа с ID {$groupId} успешно сохранена.");
                } else {
                    Log::info("Запись для группы с ID {$groupId} и user_id {$userId} уже существует.");
                }
            } else {
                Log::error('User not found in vk_page_users for vk_user_id: ' . $vkUserId);
            }
        } else {
            Log::error('No group found or invalid response structure from VK API.', $data);
        }
    } else {
        Log::error('VK API request failed.', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
    }
}


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
//         $groupName = $data['response'][0]['name'] ?? null;

//         // Логируем извлечённые данные
//         Log::info('Extracted group data:', [
//             'group_id' => $groupId,
//             'group_name' => $groupName,
//             'group_link' => $groupLink,
//         ]);

//         // Сохраняем ID группы, имя и ссылку в базу данных
//         DB::table('user_groups')->updateOrInsert(
//             ['user_id' => $userId, 'group_id' => $groupId],
//             ['user_id' => $userId, 'group_id' => $groupId, 'group_name' => $groupName, 'group_link' => $groupLink]
//         );

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


public function showGroup()
{
    $group = DB::table('user_groups')->where('user_id', auth()->id())->first();

    return view('vk.vk-user', [
        'group_id' => $group->group_id ?? null,
        'group_link' => $group->group_link ?? null,
    ]);
}

}
