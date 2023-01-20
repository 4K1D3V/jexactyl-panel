import { format } from 'date-fns';
import classNames from 'classnames';
import * as Icon from 'react-feather';
import React, { useEffect } from 'react';
import { useFlashKey } from '@/plugins/useFlash';
import { useSSHKeys } from '@/api/account/ssh-keys';
import ContentBox from '@/components/elements/ContentBox';
import GreyRowBox from '@/components/elements/GreyRowBox';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import PageContentBlock from '@/components/elements/PageContentBlock';
import CreateSSHKeyForm from '@/components/dashboard/forms/CreateSSHKeyForm';
import DeleteSSHKeyButton from '@/components/dashboard/ssh/DeleteSSHKeyButton';

export default () => {
    const { clearAndAddHttpError } = useFlashKey('account');

    const { data, isValidating, error } = useSSHKeys({
        revalidateOnMount: true,
        revalidateOnFocus: false,
    });

    useEffect(() => {
        clearAndAddHttpError(error);
    }, [error]);

    return (
        <PageContentBlock
            title={'Account SSH'}
            description={'Create SSH keys to connect to your servers.'}
            showFlashKey={'account'}
        >
            <div className={'j-up md:flex flex-nowrap my-10'}>
                <ContentBox title={'Add SSH Key'} className={`flex-none w-full md:w-1/2`}>
                    <CreateSSHKeyForm />
                </ContentBox>
                <ContentBox title={'SSH Keys'} className={`flex-1 overflow-hidden mt-8 md:mt-0 md:ml-8`}>
                    <SpinnerOverlay visible={!data && isValidating} />
                    {!data || !data.length ? (
                        <p className={`text-center text-sm`}>
                            {!data ? 'Loading...' : 'No SSH Keys exist for this account.'}
                        </p>
                    ) : (
                        data.map((key, index) => (
                            <GreyRowBox
                                key={key.fingerprint}
                                className={classNames(
                                    `bg-neutral-600 flex space-x-4 items-center`,
                                    index > 0 && `mt-2`
                                )}
                            >
                                <Icon.Key className={`text-neutral-300`} />
                                <div className={`flex-1`}>
                                    <p className={`text-sm break-words font-medium`}>{key.name}</p>
                                    <p className={`text-xs mt-1 font-mono truncate`}>SHA256:{key.fingerprint}</p>
                                    <p className={`text-xs mt-1 text-neutral-300 uppercase`}>
                                        Added on:&nbsp;
                                        {format(key.createdAt, 'MMM do, yyyy HH:mm')}
                                    </p>
                                </div>
                                <DeleteSSHKeyButton name={key.name} fingerprint={key.fingerprint} />
                            </GreyRowBox>
                        ))
                    )}
                </ContentBox>
            </div>
        </PageContentBlock>
    );
};
