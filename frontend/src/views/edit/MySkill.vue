<template>
    <section id="my-skill">
        <h3 class="section-title">
            {{ $route.meta.isEditing ? 'Редактирование' : 'Добавление' }}
            навыка
        </h3>
        <div class="add">
            <div class="add__inputs-section">
                <InputItem class="input-item--full" label="Название" placeholder="Название" id="skill-title"
                    name="skill_title"></InputItem>
            </div>
            <div class="add__load-media">
                <div class="add__load-media-item">
                    <LoadImage title="Изображение" ref="imageLoadComponent" v-model="imageId"></LoadImage>
                </div>
                <div class="add__load-media-item">
                    <LoadVideo title="Видео" ref="videoLoadComponent" v-model="videoId"></LoadVideo>
                </div>
            </div>
            <div class="add__buttons">
                <button v-if="$route.meta.isEditing" class="button button--color_2" type="button" @click="update">
                    Редактировать
                </button>
                <button v-if="$route.meta.isEditing" class="button" type="button" @click="remove">
                    Удалить
                </button>
                <button v-else class="button button--color_2" type="button" @click="load">
                    Добавить
                </button>
            </div>
        </div>
    </section>
</template>

<script>
import LoadImage from '@/components/private/LoadImage.vue';
import LoadVideo from '@/components/private/LoadVideo.vue';

export default {
    name: 'MySkill',
    components: {
        LoadImage,
        LoadVideo
    },
    data() {
        return {
            videoId: null,
            imageId: null
        }
    },
    methods: {
        async update() {

        },
        async remove() {

        },
        async load() {

        }
    },
}
</script>

<style></style>