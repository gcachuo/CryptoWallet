Project.Users = {
    fetchAmounts: async function () {
        const data = await Project.request('users/fetchAmounts', {
            user: JSON.parse(localStorage.getItem('user'))
        }, 'POST');
        return data.response.amounts;
    },
    signOut: function () {
        localStorage.removeItem('user');
        Project.navigate('sign-in');
        $("body > header").hide();
        clearInterval(Project.refreshInterval);
    }
};