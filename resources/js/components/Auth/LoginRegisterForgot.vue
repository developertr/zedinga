<template>
    <div>
        <div class="authForms">
            <img src="/images/logo.png" class="logo">
            <div class="socialMediaLogin">
                <b>Sosyal Medya hesaplarınla giriş yap</b>
                <a href=""><img src="/svg/facebook.svg" alt=""></a>
                <a href="/login/twitter"><img src="/svg/twitter.svg" alt=""></a>
                <a href=""><img src="/svg/instagram.svg" alt=""></a>
            </div>
            <div class="activationForm" v-if="formType=='activation'">
                <div class="or">mail doğrulama</div>
                <form autocomplete="off" @submit.prevent="activationSubmit()">
                    <div class="text-center">
                        Lütfen mail adresinize gönderilen<br/>doğrulama kodunu yazınız.
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control activationInput" name="" id="" aria-describedby="helpId" placeholder="" v-model="activationCode" autofocus ref="activationKey">
                    </div>
                    <button type="submit" class="btn btn-primary">Doğrula</button>
                </form>
                <div class="form-group">
                    <br/>
                    <button type="button" class="btn" @click="sendActivationCodeAgain">Tekrar Gönder</button>
                </div>
            </div>
            <div class="loginForm" v-if="formType=='login'">
                <div class="or">zaten üyeyim</div>
                <form autocomplete="off" @submit.prevent="loginSubmit()">
                    <div class="form-group">
                        <img src="/svg/avatar.svg">
                        <input type="text" class="form-control" name="" id="" aria-describedby="helpId" placeholder="Kullanıcı Adı" v-model="username">
                    </div>
                    <div class="form-group">
                        <img src="/svg/password.svg">
                        <input type="password" class="form-control" name="" id="" aria-describedby="helpId" placeholder="Şifre" v-model="password">
                        <div class="text-right">
                            <a class="btn forgotPassword" @click="changeFormType('forgot')">Şifreni mi unuttun?</a>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Giriş Yap</button>
                </form>
                <div class="or">veya</div>
                <button type="button" class="btn registerButton" @click="changeFormType('register')">Kayıt Ol</button>
            </div>
            <div class="registerForm" v-else-if="formType=='register'">
                <div class="or">üye ol</div>
                <form autocomplete="off" @submit.prevent="registerSubmit()">
                    <div class="form-group">
                        <img src="/svg/email.svg">
                        <input type="text" class="form-control" name="" aria-describedby="helpId" placeholder="Email Adresiniz" v-model="registerEmail">
                        <div class="text-right">
                            <small id="emailHelpId" class="form-text text-muted">Onay kodu gönderilecektir.</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <img src="/svg/avatar.svg">
                        <input type="text" class="form-control" name="" aria-describedby="helpId" placeholder="Kullanıcı Adı" v-model="registerUsername">
                    </div>
                    <div class="form-group">
                        <img src="/svg/password.svg">
                        <input type="password" class="form-control" name="" aria-describedby="helpId" placeholder="Şifre" v-model="registerPassword">
                    </div>
                    <div class="form-group">
                        <img src="/svg/password.svg">
                        <input type="password" class="form-control" name="" aria-describedby="helpId" placeholder="Şifre Tekrar" v-model="registerPasswordRepeat">
                    </div>
                    <button type="submit" class="btn btn-primary">Kayıt Ol</button>
                </form>
                <div class="or">veya</div>
                <button type="button" class="btn registerButton" @click="changeFormType('login')">Giriş Yap</button>
            </div>
            <div class="forgotPasswordForm" v-if="formType=='forgot'">
                <div class="or">şifremi unuttum</div>
                <form autocomplete="off" @submit.prevent="forgotSubmit()">
                    <div class="form-group">
                        <img src="/svg/avatar.svg">
                        <input type="text" class="form-control" name="" id="" aria-describedby="helpId" placeholder="Kullanıcı Adı" v-model="forgotUsername">
                    </div>
                    <div class="or">veya</div>
                    <div class="form-group">
                        <img src="/svg/email.svg">
                        <input type="text" class="form-control" name="" aria-describedby="helpId" placeholder="Email Adresiniz" v-model="forgotEmail">
                    </div>
                    <button type="submit" class="btn btn-primary">Şifremi Yenile</button>
                </form>
                <div class="or">veya</div>
                <button type="button" class="btn registerButton" @click="changeFormType('login')">Giriş Yap</button>
            </div>
            <div class="ForgotActivationForm" v-if="formType=='forgotActivation'">
                <div class="or">mail doğrulama</div>
                <form autocomplete="off" @submit.prevent="activationForgotSubmit()">
                    <div class="text-center">
                        Lütfen mail adresinize gönderilen<br/>doğrulama kodunu yazınız.
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control activationInput" name="" id="" aria-describedby="helpId" placeholder="" v-model="forgotActivationCode" autofocus>
                    </div>
                    <div class="form-group">
                        <img src="/svg/password.svg">
                        <input type="password" class="form-control" name="" aria-describedby="helpId" placeholder="Şifre" v-model="forgotPassword">
                    </div>
                    <div class="form-group">
                        <img src="/svg/password.svg">
                        <input type="password" class="form-control" name="" aria-describedby="helpId" placeholder="Şifre Tekrar" v-model="forgotPasswordRepeat">
                    </div>
                    <button type="submit" class="btn btn-primary">Doğrula</button>
                </form>
                <div class="form-group">
                    <br/>
                    <button type="button" class="btn" @click="forgotSubmit">Tekrar Gönder</button>
                </div>
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
                username : '',
                password : '',
                registerEmail : '',
                registerUsername : '',
                registerPassword : '',
                registerPasswordRepeat : '',
                registerErrors : [],
                activationMail : '',
                activationCode : '',
                forgotUsername : '',
                forgotEmail : '',
                forgotUserId : '',
                forgotActivationCode : '',
                forgotPassword : '',
                forgotPasswordRepeat : ''
            }
        },
        methods: {
            changeFormType(type) {
                this.formType=type;
            },
            registerSubmit(){
                this.$parent.isLoading = true;
                var app = this;
                axios.post('/register',
                    {
                        email : app.registerEmail,
                        username : app.registerUsername,
                        password : app.registerPassword,
                        password_confirmation : app.registerPasswordRepeat
                    }
                ).then(response=>{
                    if(response.status==200) {
                        this.$parent.isLoading = false;
                        if(response.data.success) {
                            app.activationMail = app.registerEmail;
                            app.formType = 'activation';
                            this.$snotify.success('Doğrulama kodunuz için mail adresinizi kontrol edin');
                        } else {
                            this.$parent.showErrors(response.data.message);
                        }
                    }
                }).catch(error=>{
                    this.$parent.showErrors({'message':['Beklenmedik bir hata oluştu. Lütfen daha sonra tekrar deneyiniz']});
                })
            },
            sendActivationCodeAgain() {
                this.$parent.isLoading = true;
                var app = this;
                axios.post('/activation-send-again',
                    {
                        email : app.activationMail,
                    }
                ).then(response=>{
                    if(response.status==200) {
                        this.$parent.isLoading = false;
                        if(response.data.success) {
                            this.$snotify.success('Yeni doğrulama kodunuz gönderildi');
                        } else {
                            this.$parent.showErrors(response.data.message);
                        }
                    }
                }).catch(error=>{
                    this.$parent.showErrors({'message':['Beklenmedik bir hata oluştu. Lütfen daha sonra tekrar deneyiniz']});
                })
            },
            activationSubmit() {
                this.$parent.isLoading = true;
                var app = this;
                axios.post('/activation',
                    {
                        email : app.activationMail,
                        code : app.activationCode
                    }
                ).then(response=>{
                    if(response.status==200) {
                        this.$parent.isLoading = false;
                        if(response.data.success) {
                            location.href='/profile';
                        } else {
                            this.$parent.showErrors(response.data.message);
                        }
                    }
                }).catch(error=>{
                    this.$parent.showErrors({'message':['Beklenmedik bir hata oluştu. Lütfen daha sonra tekrar deneyiniz']});
                })
            },
            loginSubmit() {
                this.$parent.isLoading = true;
                var app = this;
                axios.post('/login',
                    {
                        username : app.username,
                        password : app.password
                    }
                ).then(response=>{
                    if(response.status==200) {
                        this.$parent.isLoading = false;
                        if(response.data.success) {
                            location.href='/profile';
                        } else {
                            this.$parent.showErrors(response.data.message);
                        }
                    }
                }).catch(error=>{
                    this.$parent.showErrors({'message':['Beklenmedik bir hata oluştu. Lütfen daha sonra tekrar deneyiniz']});
                })
            },
            forgotSubmit() {
                this.$parent.isLoading = true;
                var app = this;
                axios.post('/forgot',
                    {
                        username : app.forgotUsername,
                        email : app.forgotEmail
                    }
                ).then(response=>{
                    if(response.status==200) {
                        this.$parent.isLoading = false;
                        if(response.data.success) {
                            app.forgotUserId = response.data.userid;
                            this.$snotify.success('Doğrulama kodunuz gönderildi');
                            app.formType = 'forgotActivation';
                        } else {
                            this.$parent.showErrors(response.data.message);
                        }
                    }
                }).catch(error=>{
                    this.$parent.showErrors({'message':['Beklenmedik bir hata oluştu. Lütfen daha sonra tekrar deneyiniz']});
                })
            },
            activationForgotSubmit() {
                this.$parent.isLoading = true;
                var app = this;
                axios.post('/forgot-password-update',
                    {
                        userid : app.forgotUserId,
                        code : app.forgotActivationCode,
                        password : app.forgotPassword,
                        password_confirmation : app.forgotPasswordRepeat
                    }
                ).then(response=>{
                    if(response.status==200) {
                        this.$parent.isLoading = false;
                        if(response.data.success) {
                            location.href='/profile';
                        } else {
                            this.$parent.showErrors(response.data.message);
                        }
                    }
                }).catch(error=>{
                    this.$parent.showErrors({'message':['Beklenmedik bir hata oluştu. Lütfen daha sonra tekrar deneyiniz']});
                })
            }
        }
    }
</script>

<style scoped>

</style>
