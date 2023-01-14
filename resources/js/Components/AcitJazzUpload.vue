<template>
    <div class="ac-component">
       <div class="ac-upload-container">
        <input type="file" ref="filePond" name="file"  />
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
    props:['title','folder','modelValue','placeholder','settings','name','filetype'],
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
                    load: './',
                    fetch: './media/fetch/',
                },
                maxFiles: 1,
                allowMultiple:false,
                allowReorder:true,
                required: false,
                credits:false,
                labelIdle:'Browse',
                files:[],
            },
            resfile:null,
            files:[],
        }
    },
    created(){
        this.resfile = this.modelValue;
        this.options.labelIdle = this.placeholder ?? '  <i class="fas text-blue-400 fa-cloud-arrow-up"></i><br> Drag & Drop your files or <span class="filepond--label-action"> Browse </span>'
        this.mapFiles(this.modelValue)
    },
    methods:{
        addFiles(newfile){
            this.files.push(JSON.parse(newfile));
            this.resfile = this.files;
            this.$emit('update:modelValue',this.resfile);
            this.$emit('uploaded',this.resfile);
            this.mapFiles(this.resfile)
        },
        mapFiles(files){
            try{
                let extensions =['jpg','jpeg','gif','png','webp'];
                var filedata  = files.map( (file) => {
                    return {
                        media:file,
                        source : extensions.includes(file.extension) ? 'media/'+file.path : 'storage/'+file.path,
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
                 this.resfile = this.options.files;
                this.$emit('update:modelValue',this.resfile);
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
.filepond--drop-label.filepond--drop-label label{
    cursor: pointer;
    font-size: 12px;
}
.filepond--drop-label.filepond--drop-label label i{
    font-size: 20px;
}
.filepond--root{
    margin: 0;
}
.filepond--root,
.filepond--root .filepond--drop-label {
  height: 90px;
}
</style>