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
                    <LoadImage title="Изображение" ref="imageLoadComponent"></LoadImage>
                </div>
                <div class="add__load-media-item">
                    <LoadVideo title="Видео"></LoadVideo>
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
import {
    restApiPostMethod, restApiDeleteMethod, restApiPutMethod, restApiGetMethod
} from '@/assets/scripts/scripts.js';

export default {
    name: 'MySkill',
    components: {
        LoadImage,
        LoadVideo
    },
    methods: {
        async update() {

        },
        async remove() {

        },
        async load() {
            const imageLoadComponent = this.$refs.imageLoadComponent;
            if (!imageLoadComponent)
                return;

            const image = imageLoadComponent.$refs.input.files;
            if (image.length > 0) {
                const data = { image };
                const res = await restApiPostMethod(data, 'json', { method: 'image' });
                console.log(res);
            }
        }
    }
}
</script>

<style></style>