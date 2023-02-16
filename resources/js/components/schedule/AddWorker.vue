<template>
<a-row>
  <a-col :span="12" class="container">
    <a-input
        v-model:value="searchString"
        v-on:input="searchUser"
    />
  </a-col>
  <a-col :span="12" class="container">
    <a-select v-model:value="workerRole" style="width: 100%">
      <a-select-option v-for="role in roleList">
        {{role.name}}
      </a-select-option>
    </a-select>
  </a-col>
  <a-col :span="24">
    <a-row :span="12" v-for="worker in workers" class="container">
      <div v-on:click="chooseWorker">

      </div>
    </a-row>
  </a-col>
  <a-col :span="24">
    <a-row>
      <a-col :span="12" class="container">
        <a-button v-on:click="addWorker">
          Добавить работника
        </a-button>
      </a-col>
      <a-col :span="12" class="container">
        <a-button v-on:click="$emit('closeAddWorker')">
          Отменить
        </a-button>
      </a-col>
    </a-row>
  </a-col>
</a-row>
</template>

<script>
export default {
  name: "AddWorker",
  emits: ['closeAddWorker'],
  data() {
    return {
      searchString: '',
      workers: [],
      roleList: [],

      worker: false,
      workerRole: 'Выберете роль'
    }
  },
  methods: {
    async getRoleList() {
      let response = await this.$scheduleApi.get('/get_role_list')
      this.roleList = response.data
    },
    async searchUser() {
      if (this.searchString.length > 3) {
        let response = await this.$scheduleApi.get('/search_bitrix_user')
        this.workers = response.data
      }
    },
    addWorker() {
      if (this.worker && this.workerRole) {
        this.$emit('addWorker', {
          shopId: this.shopId,
          month: this.month,
          role: this.workerRole,
          worker: this.worker,
        })
      }
    }
  }
}
</script>

<style scoped>
.container {
  padding: 5px
}
</style>