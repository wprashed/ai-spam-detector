=== AI Spam Comment Detector ===
Contributors: wprashed
Tags: comment spam, spam blocker, openai, gpt-4, ai moderation, antispam  
Requires at least: 5.6  
Tested up to: 6.5  
Requires PHP: 7.4  
Stable tag: 1.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

AI-powered comment spam detection using GPT-4. Blocks spam comments with inline user warnings—no CAPTCHA or third-party services needed.

== Description ==

**Tired of traditional spam filters missing the mark or adding friction with CAPTCHAs?**  
**AI Spam Comment Detector** uses OpenAI's GPT-4 to intelligently analyze and flag spam comments *before* they get posted — and notifies users inline right on the comment form.

🧠 **Smart Detection**: Uses context-aware GPT-4 language model  
🚫 **Auto-Block Spam**: Flags or blocks comments before submission  
🔔 **Inline Error Messaging**: Warns users above the comment form  
🔐 **Private & Secure**: No data stored externally beyond OpenAI API  
🔧 **API Key Config**: Add your own OpenAI API key from plugin settings  

No more CAPTCHAs. No more bots. Just intelligent spam protection.

== Features ==

- Detects spam comments in real time using GPT-4
- Automatically marks spam before submission
- Warns users with inline messages on the comment form
- Admin settings page to set the OpenAI API key
- Handles invalid API key or quota exceeded errors
- WordPress-standard and lightweight – no 3rd-party services needed

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/ai-spam-comment-detector` directory, or install through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to **Settings > AI Spam Detector** to add your OpenAI API key.
4. That’s it! Comments will now be filtered using GPT-4.

== Frequently Asked Questions ==

= What is the OpenAI API key used for? = 

The OpenAI API key is used to connect to OpenAI's GPT-4 model for spam detection. You can obtain your API key by signing up at [OpenAI](https://platform.openai.com/).

= How does this plugin work? = 

The plugin checks comments against GPT-4 in real-time before submission. If the comment is flagged as spam, a warning message will appear for the user, and the comment will not be submitted.

= Can I use this plugin without an API key? = 

No, an OpenAI API key is required to use the spam detection feature.

= What happens if my OpenAI quota is exceeded? = 

If your quota is exceeded, the plugin will notify you with a message saying "OpenAI quota exceeded."

= Will this replace reCAPTCHA or Akismet? =  
Yes, it can replace both if you're looking for a no-friction, intelligent spam filter powered by AI.

= Is any user data sent to OpenAI? =  
Only the comment content is sent securely to OpenAI for spam analysis. No personal data or identifiable information is sent.

= Can I customize the AI behavior? =  
Currently, this plugin uses a fixed system prompt for high accuracy. Customization options will come in future updates.

== Screenshots ==

1. Settings screen to enter your OpenAI API key
2. Inline warning shown when spam is detected

== Changelog ==

= 1.0 =
* Initial release

== License ==

This plugin is licensed under the GPLv2 or later.
