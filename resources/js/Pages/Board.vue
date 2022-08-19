<template>
    <div>
        <AddTaskPro @taskAdded="taskAdded"></AddTaskPro>
        <div class="relative p-2 flex overflow-x-auto h-full">
            <!-- Columns (Statuses) -->
            <div
                v-for="status in statuses"
                :key="status.slug"
                class="mr-6 w-4/5 max-w-xs flex-1 flex-shrink-0"
            >
                <div class="rounded-md shadow-md overflow-hidden">
                    <div class="p-3 flex justify-between bg-amber-600">
                        <h4 class="font-medium text-white">
                            {{ status.title }}
                        </h4>
                    </div>
                    <div class="p-2 flex-1 flex flex-col h-full overflow-x-hidden overflow-y-auto bg-white">
                        <draggable :list="status.tasks"
                                   v-bind="taskDragOptions"
                                   :group="{ name: 'people'+statuses.id, pull: 'clone', put: true }"
                                   :sort="true"
                                   :data-section="statuses.id"
                                   @end="handleTaskMoved">

                            <transition-group >
                            <!-- Tasks -->
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
                            <!-- ./Tasks -->
                                <!-- No Tasks -->
                                <div
                                    v-show="!status.tasks.length"
                                    class="flex-1 p-4 flex flex-col items-center justify-center"
                                >
                                    <span class="text-gray-600">No tasks yet</span>
                                </div>
                                <!-- ./No Tasks -->
                            </transition-group>
                        </draggable>
                    </div>
                </div>
            </div>
            <!-- ./Columns -->
        </div>
    </div>
</template>

<script>
import AddTaskPro from "@/Components/AddTaskPro.vue";
import AddTask from "./../Components/AddTask.vue";
// import draggable from "vuedraggable";
import { VueDraggableNext } from 'vue-draggable-next'
export default {
    name: "Board",
    components: {
        AddTask,
        AddTaskPro,
        draggable: VueDraggableNext,
    },

    data() {
        return {
            initialData : [],
            tasks:[],
            statuses: [],
            initialTaskLength: 0,
        }
    },
    methods : {
        getAllTask() {
          axios.get('/tasks/')
            .then(response => {
              this.statuses = response.data;
                console.log(this.statuses);
            })
            .catch(error => {
              console.log(error);
            });
        },

        taskAdded(payload){
            console.log(payload , 'payload');
            this.statuses.find(status => status.id === payload.status_id).tasks.push(payload);
            console.log(this.statuses)
        },

        handleTaskMoved(event) {
            axios.put("/tasks/sync", {columns: this.statuses}).then((response)=>{
                this.statuses = response.data;
            }).catch(err => {
                console.log(err.response);
            });
        },

        replace() {
            console.log('replace')
        },

        log(event) {
            const { moved, added } = event
            if (moved) console.log('moved', moved)
            if (added) console.log('added', added, added.element)
        },
    },
    computed:{
        taskDragOptions() {
            return {
                animation: 200,
                group: "task-list",
                dragClass: "status-drag"
            };
        }
    },
    mounted() {
        this.getAllTask();
    }
}
</script>

<style scoped>
.status-drag {
    transition: transform 0.5s;
    transition-property: all;
}
</style>
