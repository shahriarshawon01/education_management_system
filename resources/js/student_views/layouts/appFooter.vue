<template>
    <footer class="footer py-2 d-flex align-items-center">
        <div class="container-fluid">
            <div class="row w-100">
                <div class="col-sm-6 text-center text-sm-start">
                    <small>
                        Design & Develop by <strong>TMSS ICT LTD</strong>
                    </small> © {{ currentYear }} 
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
export default {
    name: "AppFooter",
    data() {
        return {
            currentYear: new Date().getFullYear()
        }
    }
}
</script>

<style scoped>
.footer {
    font-size: 14px;
    color: #ffffff;
    background-color: #434e74;
}
</style>