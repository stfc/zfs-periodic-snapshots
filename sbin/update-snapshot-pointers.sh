#!/bin/bash

function confirmed {
    confirm="n"
    read -rp '[y/N/q] > ' confirm
    if [[ "$confirm" == "q" ]]; then
        exit 2
    elif [[ "$confirm" == "y" ]]; then
        return 0
    fi
    return 1
}

function prompt_to_update_link {
    if [[ $# -ne 2 ]]; then exit 2; fi

    link_name="$1"
    link_name_title_case="$(echo "$link_name" | sed 's/.*/\L&/; s/[a-z]*/\u&/g')"
    new_snapshot="$2"
    new_target="/${new_snapshot//@//.zfs/snapshot/}/www"
    old_target="$(readlink "/data/pointers/$link_name")"

    if [[ "$new_target" != "$old_target" ]]; then
        echo "$link_name_title_case is currently: $old_target"
        echo -n "Should I change it to: $new_target "
        if confirmed; then
            ln -fsT "$new_target" "/data/pointers/$link_name"
            echo "Okay, Link updated."
        else
            echo "Okay, left link unchanged."
        fi
    else
        echo "$link_name_title_case does not need updating ($new_snapshot)"
    fi
    echo
}

ts_twoweeksago="$(date -d '2 weeks ago' +%s)"
ts_onemonthago="$(date -d '1 month ago' +%s)"

if ! command -v zfs &> /dev/null; then
    echo "ERROR: zfs command not available"
    exit 2
fi

readarray -t snapshots < <(zfs list -t snapshot -Ho name)

# Default to the three most recent snapshots
snapshot_previous="${snapshots[-3]}"
snapshot_current="${snapshots[-2]}"
snapshot_next="${snapshots[-1]}"

# Try and find snapshots which meet our policy
for snapshot in "${snapshots[@]}"; do
    ts_snapshot="$(date -d "$(echo "$snapshot" | cut -d @ -f 2)" +%s)"
    if [[ $ts_snapshot -le $ts_twoweeksago ]]; then
        snapshot_current="$snapshot"
    fi
    if [[ $ts_snapshot -le $ts_onemonthago ]]; then
        snapshot_previous="$snapshot"
    fi
done

# Ask user whether to update or not
prompt_to_update_link "next" "$snapshot_next"
prompt_to_update_link "current" "$snapshot_current"
prompt_to_update_link "previous" "$snapshot_previous"
