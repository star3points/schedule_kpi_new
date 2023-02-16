<template>
    <div>
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: 'App',
    created() {
      console.log('app_created')
    }
}
</script>

<style scoped>

</style>
