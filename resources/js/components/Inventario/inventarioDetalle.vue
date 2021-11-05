<template>
    <div class="content">
        <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">

            <v-overlay :value="loader" :z-index="'99999999'">
                <v-progress-circular
                    indeterminate
                    size="80"
                    color="grey darken-4"
                ></v-progress-circular>
            </v-overlay>

            
        </div>
    </div>
</template>

<script>
export default {
    
}
</script>
