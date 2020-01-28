Project.Users = {
    fetchAmounts: async function () {
        const {status, code, response: {message, data: {amounts}}, error} = await Project.request('users/fetchAmounts', {
            user: JSON.parse(localStorage.getItem('user'))
        }, 'POST');
        return amounts;
    },
    fetchClients: function () {
        return Project.request('users/fetchClients', {user: JSON.parse(localStorage.getItem('user'))}, 'POST');
    },
    signOut: function () {
        localStorage.removeItem('user');
        Project.navigate('sign-in');
        $("body > header").hide();
    }
};
