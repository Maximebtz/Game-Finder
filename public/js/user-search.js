document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('user-search');
    const userList = document.getElementById('user-list');

    searchInput.addEventListener('input', () => {
        if (searchInput.value.length > 2) {
            // Make an AJAX request to the user_search route
            fetch('/user/search?query=' + searchInput.value)
                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    // Clear the user list before updating it
                    userList.innerHTML = '';

                    if (data.length === 0) {
                        userList.innerHTML = '<p class="text-center">Aucun utilisateur trouvé</p>';
                    }

                    data.forEach(user => {
                        let username = user.username;
                        let addUrl = '/follow/' + user.id;
                        let followedElement;

                        if (user.is_followed) {
                            followedElement = '<span class="text-gray-500">Suivi</span>';
                        } else if (user.pending_follow) {
                            followedElement = '<span class="text-gray-500">Ajouté 🕔</span>';
                        } else {
                            followedElement = `<a href="${addUrl}" class="text-purple hover:opacity-60">Ajouter</a>`;
                        }

                        userList.innerHTML += `<p class="w-full flex justify-between">${username} ${followedElement}</p>`;
                        if (data.indexOf(user) !== data.length - 1) {
                            userList.innerHTML += '<hr class="bg-zinc-200 border-b my-2">';
                        }
                    });
                    userList.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else {
            userList.classList.add('hidden');
        }
    });
});