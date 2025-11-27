<template>
    <div>
        <template v-if="data.image">
            <img @click="openFile(getImage(data.image, data.image))" :class="`${imageClass} pointer`" :src="getImage(data.image, 'images/memers.png')">
        </template>
        <template v-else>
            <img :class="imageClass" :src="getImage(null, (data.PBI_SEX == 'Female') ? 'images/female_staff.jpg' : 'images/male_stuff.png')">
        </template>
    </div>
</template>
<script>
export default {
    name: "userImage",
    props: {
        data: {
            type: [Array, Object],
            default(){
                return {}
            }
        },
        imageClass: {
            type: [String],
            default: 'list_image'
        },
    },
}
</script>

<style scoped>

</style>
