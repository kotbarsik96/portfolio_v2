<template>
    <section id="my-skill">
        <h3 class="section-title">
            Добавление навыка
        </h3>
        <div class="add">
            <div class="add__inputs-section">
                <InputItem class="input-item--full" label="Название" placeholder="Название" id="skill-title"
                    name="skill_title"></InputItem>
            </div>
            <div class="add__load-media">
                <div class="add__load-media-item">
                    <LoadImage title="Изображение"></LoadImage>
                </div>
                <div class="add__load-media-item">
                    <LoadVideo title="Видео"></LoadVideo>
                </div>
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
    }
}
</script>

<style></style>