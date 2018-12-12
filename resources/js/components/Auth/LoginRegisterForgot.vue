<template>
    <div>
        <div class="authForms">
            <img src="/images/logo.png" class="logo">
            <div class="socialMediaLogin">
                <b>Sosyal Medya hesaplarınla giriş yap</b>
                <a href=""><img src="/svg/facebook.svg" alt=""></a>
                <a href=""><img src="/svg/twitter.svg" alt=""></a>
                <a href=""><img src="/svg/instagram.svg" alt=""></a>
            </div>
            <div class="loginForm" v-if="formType=='login'">
                <div class="or">zaten üyeyim</div>
                <div class="form-group">
                    <img src="/svg/avatar.svg">
                    <input type="text" class="form-control" name="" id="" aria-describedby="helpId" placeholder="Kullanıcı Adı">
                </div>
                <div class="form-group">
                    <img src="/svg/password.svg">
                    <input type="password" class="form-control" name="" id="" aria-describedby="helpId" placeholder="Şifre">
                    <div class="text-right">
                        <button class="btn forgotPassword" @click="changeFormType('forgot')">Şifreni mi unuttun?</button>
                    </div>
                </div>
                <button type="button" class="btn btn-primary">Giriş Yap</button>
                <div class="or">veya</div>
                <button type="button" class="btn registerButton" @click="changeFormType('register')">Kayıt Ol</button>
            </div>
            <div class="loginForm" v-else-if="formType=='register'">
                <div class="or">üye ol</div>
                <form autocomplete="off" @submit.prevent="registerSubmit()">
                    <div class="form-group">
                        <img src="/svg/email.svg">
                        <input type="text" class="form-control" name="" aria-describedby="helpId" placeholder="Email Adresiniz" v-model="registerEmail">
                        <div class="text-right">
                            <small id="emailHelpId" class="form-text text-muted">Onay kodu gönderilecektir.</small>
                        </div>
                        <div v-if="registerErrors.email" class="alert alert-danger text-right">{{ registerErrors.email }}</div>
                    </div>
                    <div class="form-group">
                        <img src="/svg/avatar.svg">
                        <input type="text" class="form-control" name="" aria-describedby="helpId" placeholder="Kullanıcı Adı" v-model="registerUsername">
                        <div v-if="registerErrors.username" class="alert alert-danger text-right">{{ registerErrors.username }}</div>
                    </div>
                    <div class="form-group">
                        <img src="/svg/password.svg">
                        <input type="password" class="form-control" name="" aria-describedby="helpId" placeholder="Şifre" v-model="registerPassword">
                        <div v-if="registerErrors.password" class="alert alert-danger text-right">{{ registerErrors.password }}</div>
                    </div>
                    <div class="form-group">
                        <img src="/svg/password.svg">
                        <input type="password" class="form-control" name="" aria-describedby="helpId" placeholder="Şifre Tekrar" v-model="registerPasswordRepeat">
                        <div v-if="registerErrors.password_confirmation" class="alert alert-danger text-right">{{ registerErrors.password_confirmation }}</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Kayıt Ol</button>
                </form>
                <div class="or">veya</div>
                <button type="button" class="btn registerButton" @click="changeFormType('login')">Giriş Yap</button>
            </div>
            <div class="loginForm" v-if="formType=='forgot'">
                <div class="or">şifremi unuttum</div>
                <div class="form-group">
                    <img src="/svg/avatar.svg">
                    <input type="text" class="form-control" name="" id="" aria-describedby="helpId" placeholder="Kullanıcı Adı">
                </div>
                <button type="button" class="btn btn-primary">Şifremi Yenile</button>
                <div class="or">veya</div>
                <button type="button" class="btn registerButton" @click="changeFormType('register')">Giriş Yap</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "LoginRegisterForgot",
        props: {
            form: {
                type : String,
                default : 'login'
            }
        },
        data() {
            return {
                formType : this.form,
                registerEmail : '',
                registerUsername : '',
                registerPassword : '',
                registerPasswordRepeat : '',
                registerErrors : []
            }
        },
        methods: {
            changeFormType(type) {
                this.formType=type;
            },
            registerSubmit(){
                var app = this;
                this.$parent.showLoader = true;
                axios.post('/register',
                    {
                        email : app.registerEmail,
                        username : app.registerUsername,
                        password : app.registerPassword,
                        password_confirmation : app.registerPasswordRepeat
                    }
                ).then(response=>{
                    if(response.status==200) {

                    }
                }).catch(error=>{
                    if(error.response.status) {
                        this.$parent.showErrors(error.response.data.errors);
                    }
                })
            }
        }
    }
</script>

<style scoped>

</style>
