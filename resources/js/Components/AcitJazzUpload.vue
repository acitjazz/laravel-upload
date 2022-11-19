<template>
    <div class="ac-component">
       <div class="ac-upload-container">
        <input type="file" ref="filePond" name="file" />
        <textarea :name="name" v-model="resfile" style="display:none"></textarea>
       </div>
       <div class="ac-slot">
         <slot></slot>
       </div>
    </div>
  </template>
  
<script>
import * as FilePond from 'filepond';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation';

FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginImageExifOrientation,
    FilePondPluginFileValidateType);
export default {
    props:['title','folder','value','placeholder','settings','name','filetype'],
    data () {
        return {
            options:{
                server:{
                    url: '/',
                    timeout: 7000,
                    process: {
                        url: './media/upload',
                        method: 'POST',
                        headers: {
                            'Accept' : 'application/json'
                        },
                        withCredentials: false,
                        onload: (response) => {
                            this.addFiles(response);
                            return response;
                        },
                        onerror: (response) => response.data,
                        ondata: (formData) => {
                            formData.append('name', this.title);
                            formData.append('folder', this.folder);
                            return formData;
                        }
                    },
                    revert: './media/destroy',
                    restore: './media/restore/',
                    load: './media/',
                    fetch: './media/fetch/',
                },
                maxFiles: 1,
                allowMultiple:false,
                allowReorder:true,
                required: true,
                credits:false,
                labelIdle:this.placeholder ?? 'Drag & Drop your files or <span class="filepond--label-action"> Browse </span>',
                files:[],
            },
            resfile:null,
            files:[],
        }
    },
    created(){
        this.resfile = this.value;
        this.mapFiles(this.value)
    },
    methods:{
        addFiles(newfile){
            this.files.push(JSON.parse(newfile));
            this.resfile = JSON.stringify(this.files);
            this.mapFiles(this.resfile)
        },
        mapFiles(files){
            try{
                var filedata  = JSON.parse(files).map( (file) => {
                    return {
                        media:file,
                        source : file.path,
                        options: {
                            type: 'local',
                            metadata: {
                                id: file.id,
                                name: file.name,
                                url: file.url,
                                size: file.size,
                            },
                        }
                    }
                });
                this.options.files = filedata;
            }catch(e){
                this.options.files = [];
            }
        },
        removeFile(file){
            axios.delete('/media/destroy', { data: file})
            .then( (response) => {
                 this.resfile = JSON.stringify(this.options.files);
            });
        }
    },  
    mounted(){
            const inputElement = this.$refs.filePond;
            this.pond = FilePond.create(inputElement,{
                acceptedFileTypes: this.filetype ?? ['image/*'],
            });
            const options = {...this.options, ...this.settings};
            this.pond.setOptions(this.options);
            this.pond.on('removefile', (error, file) => {
                var fx = this.options.files.findIndex(f => file.source == f.source);
                var fi = this.options.files[fx];
                this.options.files.splice(fx,1);
                this.removeFile(fi.media)
            });
    }
};
</script>

<style lang="scss">
.filepond--credits{
    display: none;
}
</style>