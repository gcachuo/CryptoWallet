Project.Users = {
    fetchAmounts: async function () {
        const data = await Project.request('users/fetchAmounts', {
            user: JSON.parse(localStorage.getItem('user'))
        }, 'POST');
        return data.response.amounts;
    },
    fetchClients: function () {
        return Project.request('users/fetchClients', {user: JSON.parse(localStorage.getItem('user'))}, 'POST');
    },
    signOut: function () {
        localStorage.removeItem('user');
        Project.navigate('sign-in');
    },
    loggedUser: function () {
        if (!localStorage.getItem('user')) {
            $("body > main > header").hide();
            $(".main").hide();
            if (Project.getCookie('page') !== 'sign-in') {
                Project.navigate('sign-in');
            }
        } else {
            $("body > main > header").css('display', 'flex');
            $(".main").show();
            const user = JSON.parse(localStorage.getItem('user'));
            if (user.perfil === '0') {
                $(".clientes").show();
            }
            if (Project.getCookie('page') !== 'dashboard') {
                Project.navigate('dashboard');
            }
        }
    }
};