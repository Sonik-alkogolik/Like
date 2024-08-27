$(document).ready(function() {
 

    $('#get-groups').click(function() {
        $.ajax({
            url: getGroupsUrl,
            type: 'POST',
            data: {
                user_id: userId,
                access_token: 'vk1.a.3WgPlNrH6DgZwgYKwFVJA63Is_2kkcAmSL_Ng9Oh00uFPblO-n6LbF3xxVBOoIoL9c0yuhycoET-Ofz2959vbduOxv3cdi87KyhlV-AAKb4-CrJHQYaox2uDkxR949SonlOaI1sKiLisEL58P0zmUr3GrmI4JI5je3p_00OP-5U0hr6ZxjhFXAaRfGGMK7P1y0QLjplxYonC0gqhlbOLng',
                v: '5.199',
                extended: 1
            },
            success: function(response) {
                var groupsList = $('#groups-list');
                groupsList.empty();
    
                // Проверяем, есть ли в ответе группы
                if (response.response && response.response.items) {
                    var groupsArray = [];
    
                    $.each(response.response.items, function(index, group) {
                        var groupData = {
                            screen_name: group.screen_name,
                            id: group.id
                        };
                        groupsArray.push(groupData);
    
                        // Отображаем группы на странице
                        var listItem = $('<li>').text('Screen name: ' + group.screen_name + ', ID: ' + group.id);
                        groupsList.append(listItem);
                    });
    
                    // Сохраняем массив групп в localStorage
                    localStorage.setItem('vkGroups', JSON.stringify(groupsArray));
                } else {
                    groupsList.html('<p>Группы не найдены.</p>');
                }
            },
            error: function(xhr, status, error) {
                $('#groups-list').html('<p>Произошла ошибка: ' + error + '</p>');
            }
        });
    });


    $('#check-group').click(function() {
        // Получаем ID группы из статичного элемента
        var groupId = $('#group_num').text().trim().match(/(\d+)/)[0]; // Извлекаем только число
        console.log('ID группы для проверки:', groupId);
    
        // Выполняем AJAX-запрос для получения групп
        $.ajax({
            url: getGroupsUrl,
            type: 'POST',
            data: {
                user_id: userId,
                access_token: 'vk1.a.3WgPlNrH6DgZwgYKwFVJA63Is_2kkcAmSL_Ng9Oh00uFPblO-n6LbF3xxVBOoIoL9c0yuhycoET-Ofz2959vbduOxv3cdi87KyhlV-AAKb4-CrJHQYaox2uDkxR949SonlOaI1sKiLisEL58P0zmUr3GrmI4JI5je3p_00OP-5U0hr6ZxjhFXAaRfGGMK7P1y0QLjplxYonC0gqhlbOLng',
                v: '5.199',
                extended: 1
            },
            success: function(response) {
                var groupsArray = [];
                var resultDiv = $('#group-check-result');
    
                // Проверяем, есть ли в ответе группы
                if (response.response && response.response.items) {
                    $.each(response.response.items, function(index, group) {
                        var groupData = {
                            screen_name: group.screen_name,
                            id: group.id
                        };
                        groupsArray.push(groupData);
                    });
    
                    // Сохраняем массив групп в localStorage
                    localStorage.setItem('vkGroups', JSON.stringify(groupsArray));
    
                    // Проверяем, существует ли группа с заданным ID в списке
                    var groupExists = groupsArray.some(function(group) {
                        return group.id == groupId;
                    });
    
                    if (groupExists) {
                        resultDiv.html('<p>Группа с ID ' + groupId + ' существует в вашем списке групп.</p>');
                    } else {
                        resultDiv.html('<p>Группа с ID ' + groupId + ' не найдена в вашем списке групп.</p>');
                    }
                } else {
                    resultDiv.html('<p>Группы не найдены.</p>');
                }
            },
            error: function(xhr, status, error) {
                $('#group-check-result').html('<p>Произошла ошибка: ' + error + '</p>');
            }
        });
    });
    
    // $('#check-group').click(function() {
    //     console.log(true);
        
    //     var groupId = $('#group_num').text().trim();
    //         console.log(groupId);
            
    //     $.ajax({
    //         url: getGroupsUrl,
    //         type: 'POST',
    //         data: {
    //             user_id: userId,
    //             access_token: 'vk1.a.3WgPlNrH6DgZwgYKwFVJA63Is_2kkcAmSL_Ng9Oh00uFPblO-n6LbF3xxVBOoIoL9c0yuhycoET-Ofz2959vbduOxv3cdi87KyhlV-AAKb4-CrJHQYaox2uDkxR949SonlOaI1sKiLisEL58P0zmUr3GrmI4JI5je3p_00OP-5U0hr6ZxjhFXAaRfGGMK7P1y0QLjplxYonC0gqhlbOLng',
    //             v: '5.199',
    //             extended: 1
    //         },
    //         success: function(groupsResponse) {
    //             if (groupsResponse && groupsResponse.response && groupsResponse.response.items) {
    //                 var groups = groupsResponse.response.items;
    
    //                 // После получения групп, вызываем checkGroup для проверки наличия конкретной группы
    //                 $.ajax({
    //                     url: checkgroup,
    //                     type: 'POST',
    //                     data: {
    //                         user_id: userId,
    //                         group_id: groupId,
    //                         groups: groups, // Передаем список групп в checkGroup
    //                         access_token: 'vk1.a.3WgPlNrH6DgZwgYKwFVJA63Is_2kkcAmSL_Ng9Oh00uFPblO-n6LbF3xxVBOoIoL9c0yuhycoET-Ofz2959vbduOxv3cdi87KyhlV-AAKb4-CrJHQYaox2uDkxR949SonlOaI1sKiLisEL58P0zmUr3GrmI4JI5je3p_00OP-5U0hr6ZxjhFXAaRfGGMK7P1y0QLjplxYonC0gqhlbOLng',
    //                         v: '5.199',
    //                         extended: 1
    //                     },
    //                     success: function(response) {
                            
    //                         var resultDiv = $('#group-check-result');
    //                         console.log('Response from checkGroup:', response); 
    //                         if (response.exists) {
    //                             resultDiv.html('<p>Группа с ID ' + groupId + ' существует в вашем списке групп.</p>');
    //                         } else {
    //                             resultDiv.html('<p>Группа с ID ' + groupId + ' не найдена в вашем списке групп.</p>');
    //                         }
    //                     },
    //                     error: function(xhr, status, error) {
    //                         $('#group-check-result').html('<p>Произошла ошибка: ' + error + '</p>');
    //                     }
    //                 });
    //             } else {
    //                 $('#group-check-result').html('<p>Не удалось получить список групп.</p>');
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             $('#group-check-result').html('<p>Произошла ошибка: ' + error + '</p>');
    //         }
    //     });
    // });

//     $('#check-group').click(function() {
//     // Получаем ID группы из div
//     var groupId = $('#group_num').text().trim();
    
//     $.ajax({
//         url: '/app/like/public/check-group',
//         type: 'POST',
//         data: {
//             user_id: userId,
//             group_id: groupId,
//             access_token: 'vk1.a.3WgPlNrH6DgZwgYKwFVJA63Is_2kkcAmSL_Ng9Oh00uFPblO-n6LbF3xxVBOoIoL9c0yuhycoET-Ofz2959vbduOxv3cdi87KyhlV-AAKb4-CrJHQYaox2uDkxR949SonlOaI1sKiLisEL58P0zmUr3GrmI4JI5je3p_00OP-5U0hr6ZxjhFXAaRfGGMK7P1y0QLjplxYonC0gqhlbOLng',
//             v: '5.199',
//             extended: 1
//         },
//         success: function(response) {
//             var resultDiv = $('#group-check-result');
//             if (response.exists) {
//                 resultDiv.html('<p>Группа с ID ' + groupId + ' существует в вашем списке групп.</p>');
//             } else {
//                 resultDiv.html('<p>Группа с ID ' + groupId + ' не найдена в вашем списке групп.</p>');
//             }
//         },
//         error: function(xhr, status, error) {
//             $('#group-check-result').html('<p>Произошла ошибка: ' + error + '</p>');
//         }
//     });
// });

//     $('#save-group').on('click', function() {
//     var groupLink = $('#group-link').val();
//     var accessToken = 'vk1.a.3WgPlNrH6DgZwgYKwFVJA63Is_2kkcAmSL_Ng9Oh00uFPblO-n6LbF3xxVBOoIoL9c0yuhycoET-Ofz2959vbduOxv3cdi87KyhlV-AAKb4-CrJHQYaox2uDkxR949SonlOaI1sKiLisEL58P0zmUr3GrmI4JI5je3p_00OP-5U0hr6ZxjhFXAaRfGGMK7P1y0QLjplxYonC0gqhlbOLng';


//     $.ajax({
//         url: '/app/like/public/save-group',
//         method: 'POST',
//         data: {
//             group_link: groupLink,
//             user_id: userId,
//             access_token: accessToken,
//             v: '5.199',
//         },
//         success: function(response) {
//             if (response.success) {
//                 $('#result-container').html(response.html);
//             } else {
//                 console.log('Error: ' + response.message);
//             }
//         },
//         error: function(xhr) {
//             console.error('AJAX Error: ' + xhr.responseText);
//         }
//     });
//  });

//     $('#check-group').click(function() {
//     // Получаем текст из div, а затем извлекаем числовой ID
//     var groupText = $('#210152438').text().trim();
//     var groupId = groupText.match(/\d+/)[0]; // Извлекаем только числовой ID

//     $.ajax({
//         url: '/app/like/public/get-groups-and-check-group',
//         type: 'POST',
//         data: {
//             user_id: userId,
//             group_id: groupId,
//             access_token: 'vk1.a.3WgPlNrH6DgZwgYKwFVJA63Is_2kkcAmSL_Ng9Oh00uFPblO-n6LbF3xxVBOoIoL9c0yuhycoET-Ofz2959vbduOxv3cdi87KyhlV-AAKb4-CrJHQYaox2uDkxR949SonlOaI1sKiLisEL58P0zmUr3GrmI4JI5je3p_00OP-5U0hr6ZxjhFXAaRfGGMK7P1y0QLjplxYonC0gqhlbOLng',
//             v: '5.199',
//             extended: 1
//         },
//         success: function(response) {
//             var resultDiv = $('#group-check-result');
//             var groupList = $('#group-list');
//             groupList.empty();

//             // Вывод результата проверки группы
//             if (response.exists) {
//                 resultDiv.html('<p>Группа с ID ' + groupId + ' существует в вашем списке групп.</p>');
//             } else {
//                 resultDiv.html('<p>Группа с ID ' + groupId + ' не найдена в вашем списке групп.</p>');
//             }

//             // Проверка и вывод списка групп
//             if (response.groups && response.groups.length > 0) {
//                 response.groups.forEach(function(group) {
//                     var listItem = $('<li>').text(group);
//                     groupList.append(listItem);
//                 });
//             } else {
//                 groupList.append('<li>Нет групп для отображения.</li>');
//             }
//         },
//         error: function(xhr, status, error) {
//             $('#group-check-result').html('<p>Произошла ошибка: ' + error + '</p>');
//         }
//     });
//     });

$('#save-group').click(function(e) {
    e.preventDefault();
    console.log(true);

    var accessToken = 'vk1.a.3WgPlNrH6DgZwgYKwFVJA63Is_2kkcAmSL_Ng9Oh00uFPblO-n6LbF3xxVBOoIoL9c0yuhycoET-Ofz2959vbduOxv3cdi87KyhlV-AAKb4-CrJHQYaox2uDkxR949SonlOaI1sKiLisEL58P0zmUr3GrmI4JI5je3p_00OP-5U0hr6ZxjhFXAaRfGGMK7P1y0QLjplxYonC0gqhlbOLng';
    var version = '5.199';
    var groupLink = $('#group-link').val();
    console.log(groupLink);
    console.log(getCheckGroupsUrl);
    
    
    // Получаем ID группы из ссылки
    var groupIdMatch = groupLink.match(/vk\.com\/(.*)/);
    var groupId = groupIdMatch ? groupIdMatch[1] : null;

    if (!groupId) {
        alert('Пожалуйста, введите правильную ссылку на группу.');
        return;
    }

    $.ajax({
        url: getCheckGroupsUrl,
        type: 'POST',
        data: {
            user_id: userId,
            access_token: accessToken,
            v: version,
            group_id: groupId,
            group_link: groupLink
        },
        success: function(response) {
            console.log(response.message);
            
        },
        error: function(xhr) {
            console.log(xhr.responseJSON.error);
        }
    });
});


});