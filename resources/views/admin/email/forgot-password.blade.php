<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Reset Password</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,sans-serif;">
	<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:30px 0;">
		<tr>
			<td align="center">
				<table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;">
					<tr>
						<td style="background-color:#2b2d42;padding:30px;text-align:center;">
							<h1 style="color:#ffffff;margin:0;font-size:24px;">Mehmaan ERP</h1>
						</td>
					</tr>
					<tr>
						<td style="padding:40px 30px;">
							<h2 style="color:#333;margin:0 0 20px;">Password Reset Request</h2>
							<p style="color:#666;line-height:1.6;margin:0 0 20px;">You are receiving this email because we received a password reset request for your account.</p>
							<p style="color:#666;line-height:1.6;margin:0 0 30px;">Click the button below to reset your password:</p>
							<table cellpadding="0" cellspacing="0" style="margin:0 0 30px;">
								<tr>
									<td style="background-color:#696cff;border-radius:6px;padding:12px 30px;">
										<a href="{{ $token }}" style="color:#ffffff;text-decoration:none;font-weight:bold;font-size:14px;">Reset Password</a>
									</td>
								</tr>
							</table>
							<p style="color:#999;line-height:1.6;margin:0;font-size:12px;">If you did not request a password reset, no further action is required.</p>
						</td>
					</tr>
					<tr>
						<td style="background-color:#f4f4f4;padding:20px 30px;text-align:center;">
							<p style="color:#999;margin:0;font-size:12px;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
