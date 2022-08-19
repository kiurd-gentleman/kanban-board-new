<template>
    <div>
        <AddTaskPro v-if="initialData" @taskAdded="taskAdded" :initialData="initialData" ></AddTaskPro>
        <div class="relative p-2 flex overflow-x-auto h-full">
            <div
                v-for="status in statuses"
                :key="status.id"
                class="mr-6 w-4/5 max-w-xs flex-1 flex-shrink-0"
            >
                <div class="rounded-md shadow-md overflow-hidden">
                    <div class="p-3 flex justify-center text-2xl items-baseline bg-amber-600">
                        <h4 class="font-medium text-white">
                            {{ status.title }}
                        </h4>
                    </div>
                    <div class="p-2 flex-1 flex flex-col h-full overflow-x-hidden overflow-y-auto bg-white">
                        <vue-draggable-next :list="status.tasks"
                                   v-bind="taskDragOptions"
                                   :sort="true"
                                   @end="handleTaskMoved"
                        >
                            <div
                                v-for="task in status.tasks"
                                :key="task.id"
                                class="mb-3 p-3 h-24 flex flex-col bg-gray-200 shadow"
                            >
                                    <span class="mb-2 text-xl text-gray-900 align-middle">
                                        <p class="top-0">
                                             {{ task.title }}
                                        </p>
                                    </span>
                            </div>
                            <div
                                v-show="!status.tasks.length"
                                class="flex-1 p-4 flex flex-col items-center justify-center"
                            >
                                <span class="text-gray-600">No tasks yet</span>
                            </div>
                        </vue-draggable-next>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {computed, onMounted, reactive, ref} from "vue";
import AddTaskPro from "./../Components/AddTaskPro.vue";
import { VueDraggableNext} from 'vue-draggable-next'
export default {
    components: {VueDraggableNext, AddTaskPro},
    setup(){
        let initialData = ref(0)
        const statuses = ref({})

        async function getAllTask() {
            await axios.get('/tasks/')
                .then(response => {
                    statuses.value = response.data;
                    initialData.value = statuses.value[0].id;
                })
                .catch(error => {
                    console.log(error);
                });
        }

        function taskAdded(payload) {
            console.log(payload , 'payload');
            statuses?.value.find(status => status.id === payload.status_id).tasks.push(payload);
            console.log(statuses.value)
        }

        function handleTaskMoved(event) {
            axios.put("/tasks/sync", {columns: statuses.value}).then((response) => {
                statuses.value = response.data;
            }).catch(err => {
                console.log(err.response);
            });
        }

        const taskDragOptions = computed(() =>{
            return {
                animation: 200,
                group: "task-list",
                dragClass: "status-drag"
            };
        })

        onMounted(() => {
            getAllTask().then(() => {
                console.log(statuses.value)
            });

        })

        return {
            initialData,
            statuses,
            getAllTask,
            taskAdded,
            handleTaskMoved,
            taskDragOptions
        }

    }
}
</script>

<style scoped>

</style>
