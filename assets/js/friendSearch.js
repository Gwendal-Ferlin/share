document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('friend-search');
    const friendsList = document.getElementById('friends-list');
    const originalFriends = friendsList ? [...friendsList.children] : [];

    if (searchInput && friendsList) {
        searchInput.addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();

            // Si la recherche est vide, on remet la liste complète
            if (!searchTerm) {
                friendsList.innerHTML = '';
                originalFriends.forEach(friend => {
                    friendsList.appendChild(friend.cloneNode(true));
                });
                return;
            }

            // On filtre les amis selon le texte tapé
            const filteredFriends = originalFriends.filter(friend => {
                const name = friend.querySelector('.friend-name').textContent.toLowerCase();
                return name.includes(searchTerm);
            });

            // On affiche uniquement les amis filtrés
            friendsList.innerHTML = '';
            filteredFriends.forEach(friend => {
                friendsList.appendChild(friend.cloneNode(true));
            });
        });
    }
});



