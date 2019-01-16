<?= $this->render('partials/header') ?>


        <div id="register">

            <h1>Register</h1>

            <div id="errors" class="error"></div>

            <form method="post" action="" id="register-form">

                <input type="hidden" value="<?= $this->csrf('register-form')?>" name="token" />

                <div class="form-item">
                    <label>Name</label>
                    <input type="text" name="name" id="input-name" />
                </div>

                <div class="form-item">
                    <label>E-mail</label>
                    <input type="text" name="email" id="input-email" />
                </div>

                <div class="form-item">
                    <label>Password</label>
                    <input type="password" name="password" id="input-password" />
                </div>

                <div class="form-item">
                    <label>Repeat password</label>
                    <input type="password" name="password_confirm" id="input-password_confirm" />
                </div>

                <div class="form-item">
                    <button id="register-form-btn">Register</button>
                </div>

            </form>
        </div>


<?= $this->render('partials/footer') ?>
