<?= $this->render('partials/header') ?>


        <div id="login">

            <h2>Log in</h2>

            <div id="errors" class="error"></div>

            <form method="post" action="/login" id="login-form">

                <input type="hidden" value="<?= $this->csrf('login-form')?>" name="token" />

                <div class="form-item">
                    <label>E-mail</label>
                    <input type="text" name="email" id="login-email" />
                </div>

                <div class="form-item">
                    <label>Password</label>
                    <input type="password" name="password" id="login-password" />
                </div>

                <div class="form-item">
                    <button id="login-form-btn">Log in</button>
                </div>

            </form>

            Don't have an account? <a href="/register">Register here</a>.

        </div>


<?= $this->render('partials/footer') ?>
