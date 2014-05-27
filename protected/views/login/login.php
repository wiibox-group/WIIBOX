<div class="container page-login">
	<form id="signinForm" class="form-signin" role="form" action="">
        <h2 class="form-signin-heading">{{title}}</h2>
        <input name="email" type="email" class="form-control" placeholder="{{input1}}" required>
        <input name="password" type="password" class="form-control" placeholder="{{input2}}" required>
        <label class="checkbox">
          	<input name="remember" type="checkbox" checked="checked">{{remember}}
        </label>
        <button class="btn btn-lg btn-primary btn-block" type="submit">{{btn}}</button>
    </form>
</div>