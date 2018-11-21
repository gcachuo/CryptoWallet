Project.Users = {
    fetchAmounts: async function () {
        const data = await Project.request('users/fetchAmounts', {
            user: JSON.parse(localStorage.getItem('user'))
        }, 'POST');
        return data.response.amounts;
    }
};