<?php

/*
 * This file is part of the PHP Input package.
 *
 * (c) Francesco Bianco <bianco@javanile.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Javanile\Imap2;

class Mailbox
{
    public static function check($imap)
    {
        if (is_a($imap, Connection::class)) {
            $client = $imap->getClient();
            $imap->openMailbox();

            return (object) [
                'Driver' => 'imap',
                'Mailbox' => $imap->getMailbox(),
                'Nmsgs' => $client->data['EXISTS'],
                'Recent' => $client->data['RECENT'],
            ];
        }

        return imap_check($imap);
    }

    public static function list($imap, $reference, $pattern)
    {
        if (is_a($imap, Connection::class)) {
            $referenceParts = explode('}', $reference);
            $client = $imap->getClient();
            $return = [];
            $mailboxes = $client->listMailboxes($referenceParts[1], $pattern);
            foreach ($mailboxes as $mailbox) {
                if (in_array('\\Noselect', $client->data['LIST'][$mailbox])) {
                    continue;
                }
                $return[] = $referenceParts[0].'}'.$mailbox;
            }

            return $return;
        }

        return imap_list($imap, $reference, $pattern);
    }

    public static function createMailbox($imap, $mailbox)
    {
        if (is_a($imap, Connection::class)) {
            $client = $imap->getClient();

            return $client->createFolder($mailbox);
        }

        return imap_createmailbox($imap, $mailbox);
    }

    public static function deleteMailbox($imap, $mailbox)
    {
        if (is_a($imap, Connection::class)) {
            $client = $imap->getClient();

            return $client->deleteFolder($mailbox);
        }

        return imap_deletemailbox($imap, $mailbox);
    }
}