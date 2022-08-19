<template>
    <div>
        <form
            class="relative mb-3 flex flex-col justify-between bg-white rounded-md shadow overflow-hidden"
            @submit.prevent="handleAddNewTask"
        >
            <div class="p-3 flex-1">

                <textarea
                    class="mt-3 p-2 block w-full p-1 border text-sm rounded"
                    rows="2"
                    placeholder="Write your task"
                    v-model.trim="newTask.title"
                ></textarea>
                <div v-show="errorMessage">
        <span class="text-xs text-red-500">
          {{ errorMessage }}
        </span>
                </div>
            </div>
            <div class="p-3 flex justify-between items-end text-sm bg-gray-100">
                <button
                    type="submit"
                    class="px-3 py-1 leading-5 text-white bg-orange-600 hover:bg-orange-500 rounded"
                >
                    Add
                </button>
            </div>
        </form>
    </div>

</template>

<script>
export default {
    name: "AddTask",
    data() {
        return {
            newTask: {
                title: "",
                order: 0,
                status_id: null
            },
            errorMessage: ""
        }
    },
    methods:{
        handleAddNewTask() {
            if (!this.newTask.title) {
                this.errorMessage = "The task field is required";
                return;
            }
            axios.post("/tasks", this.newTask)
                .then(res => {
                    this.$emit("task-added", res.data);
                })
                .catch(err => {
                    this.handleErrors(err);
                });
        },
        handleErrors(err) {
            if (err.response && err.response.status === 422) {
                const errorBag = err.response.data.errors;
                if (errorBag.title) {
                    this.errorMessage = errorBag.title[0];
                } else {
                    this.errorMessage = err.response.message;
                }
            } else {
                console.log(err.response);
            }
        }
    },
    mounted() {
        this.newTask.status_id = 1;
    },
}
</script>

<style scoped>

</style>
